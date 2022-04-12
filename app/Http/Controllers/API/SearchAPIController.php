<?php

namespace App\Http\Controllers\API;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\Genre;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SearchAPIController extends AppBaseController
{
    public function autocompleteInstructor(Request $request)
    {
        $searchString = $request->input('instructor');
        $searchString = trim($searchString, '@');

        if (!$searchString) {
            return $this->sendError("instructor query param is requrid", 400);
        }

        $searchStringArr = preg_split('/\s+/', $searchString, -1, PREG_SPLIT_NO_EMPTY);

        $userQuery = User::with(['profile', 'roles']);

        foreach($searchStringArr as $searchString) {
            $userQuery->searchFromNameInstagram($searchString);
        }

        $result = $userQuery->whereHas('roles', static function (Builder $query) {
                $query->where('name', USER::ROLE_INSTRUCTOR);
            })
            ->get();

        return $this->sendResponse($result);
    }

    public function autocompleteGenres(Request $request)
    {
        $searchString = $request->input('genre');

        if (!$searchString) {
            return $this->sendError("genre query param is requrid", 400);
        }

        $result = Genre::where('title', 'LIKE', $searchString . '%')->get();

        return $this->sendResponse($result);
    }

    public function autocompleteLocations(Request $request)
    {
        $searchString = $request->input('location');
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

        $result = [];
        foreach ($cities as $city) {
            $instructorsCount = count(
                array_unique(array_merge(
                    optional($instructorIdsByCity->get($city))->toArray() ?? [],
                    optional($instructorIdsByLessons->get($city))->toArray() ?? []
                ))
            );

            $result[] = [
                'city' => $city,
                'city_count' => $lessonsCountByCity->get($city, 0),
                'instructors_count' => $instructorsCount
            ];
        }

        return $this->sendResponse($result);
    }
}
