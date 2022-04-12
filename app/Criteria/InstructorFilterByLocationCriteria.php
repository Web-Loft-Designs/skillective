<?php

namespace App\Criteria;

use App\Models\Lesson;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class InstructorFilterByLocationCriteria implements CriteriaInterface
{
	protected $location;
	protected $city = null;
	protected $state = null;

	public function __construct($location)
	{
		$this->location = $location;
		$locationDetails = getLocationDetails($this->location);
		if (isset($locationDetails['city']))
			$this->city = $locationDetails['city'];
		if (isset($locationDetails['state']))
			$this->state = $locationDetails['state'];
	}

    public function apply($model, RepositoryInterface $repository)
    {
        $searchString = $this->city;
        $nowOnServer = Carbon::now()->format('Y-m-d H:i:s');

        $lessonsCollection = Lesson::select([
            'instructor_id',
            'city'
        ])
            ->whereRaw("CONVERT_TZ('{$nowOnServer}', 'GMT', lessons.timezone_id) <= lessons.start")
            ->where('lesson_type', 'in_person')
            ->where('city', 'LIKE', $searchString . '%')
            ->get();

        $lessonsCountByCity = $lessonsCollection->groupBy(static function(Lesson $lesson) {
            return $lesson->city;
        })
            ->map
            ->count();

        $instructorIdsByLessons = $lessonsCollection
            ->groupBy(static function(Lesson $lesson) {
                return $lesson->city;
            })
            ->map(static function(Collection $lessonCollection) {
                return $lessonCollection->pluck('instructor_id')->unique();
            });

        $instructorIdsByCity = User::with('profile')
            ->select(['id'])
            ->where('status', 'active')
            ->whereHas('roles', static function (Builder $subQuery) {
                $subQuery->where('name', USER::ROLE_INSTRUCTOR);
            })
            ->whereHas('profile', static function (Builder $subQuery) use ($searchString) {
                $subQuery->where('city', 'LIKE', "%{$searchString}%");
            })
            ->get()
            ->groupBy(static function(User $user) {
                return $user->profile->city;
            })
            ->map(static function(Collection $userCollection) {
                return $userCollection->pluck('id')->unique();
            });

        $cities = $lessonsCountByCity
            ->union($instructorIdsByCity)
            ->keys()
            ->toArray();

        foreach ($cities as $city) {
            $result = array_unique(array_merge(
                optional($instructorIdsByCity->get($city))->toArray() ?? [],
                optional($instructorIdsByLessons->get($city))->toArray() ?? []
            ));
        }

        return $model->whereIn('users.id', $result);
    }
}
