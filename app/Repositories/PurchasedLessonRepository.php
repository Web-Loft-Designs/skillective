<?php

namespace App\Repositories;


use App\Models\PurchasedLesson;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchasedLessonRepository extends BaseRepository
{

    /**
     * @return string
     */
    public function model()
	{
		return PurchasedLesson::class;
	}

    /**
     * @var bool
     */
    protected $skipPresenter = true;

    /**
     * @return string
     */
    public function presenter()
	{
		return "Prettus\\Repository\\Presenter\\ModelFractalPresenter";
	}

    /**
     * @param $data
     * @return mixed
     */
    public function presentResponse($data)
	{
		return $this->presenter->present($data);
	}


    /**
     * @param Request $request
     * @param $studentUserId
     * @return LengthAwarePaginator|Collection|mixed
     */
    public function getStudentPurchasedLessons(Request $request, $studentUserId = null)
	{
		if (!$studentUserId)
			$studentUserId = Auth::user()->id;
		$this->resetCriteria();
		$this->resetScope();

        $genre = $request->input('genre');
        $search = $request->input('search');
		$this->scopeQuery(function ($query) use ($studentUserId, $genre, $search) {
            $userGenres = Auth()->user()->genres()->orderBy('title', 'desc')->get()->pluck('id')->toArray();
            $ids_ordered = implode(',', $userGenres);
			$query->join('users', 'purchased_lessons.student_id', '=', "users.id")
				->join('profiles', 'users.id', '=', "profiles.user_id")
				->join('pre_r_lessons', 'purchased_lessons.pre_r_lesson_id', '=', "pre_r_lessons.id")
				->join('genres', 'pre_r_lessons.genre_id', '=', "genres.id")
				->where('purchased_lessons.student_id', $studentUserId)
                ->when($genre, function ($query) use ($genre) {
                    $query->where('pre_r_lessons.genre_id', $genre);
                })
                ->when($search, function ($query) use ($search) {
                    $query->where('pre_r_lessons.title', 'like','%'.$search.'%');
                })
                ->orderBy('purchased_lessons.pre_r_lesson_id', 'desc')
                ->groupBy('purchased_lessons.id');

			return $query;
		});

		$this->with(['student', 'preRecordedLesson', 'preRecordedLesson.instructor', 'preRecordedLesson.instructor.profile', 'student.profile', 'preRecordedLesson.genre', 'preRecordedLesson.files']);

		if ($request->filled('limit'))
			return $this->get(['purchased_lessons.*']);
		else
			return $this->paginate(25, ['purchased_lessons.*']);
	}

    /**
     * @param $instructorId
     * @param $period
     * @return array
     */
    public function getAmountEarnedForPeriod($instructorId, $period = '')
	{

		$this->resetCriteria();
		$this->resetScope();
		$this->scopeQuery(function ($query) use ($instructorId, $period) {
			if ($period == '')
				$query = $query->select(DB::raw('SUM(purchased_lessons.price - purchased_lessons.service_fee - COALESCE(purchased_lessons.pp_processor_fee, 0) ) as sumEarnedInPeriod'), DB::raw("YEAR(purchased_lessons.created_at) as lessonsPeriod"));
			else
				$query = $query->select(DB::raw('SUM(purchased_lessons.price - purchased_lessons.service_fee - COALESCE(purchased_lessons.pp_processor_fee, 0)) as sumEarnedInPeriod'), DB::raw("MONTH(purchased_lessons.created_at) as lessonsPeriod"))
					->whereRaw("YEAR(purchased_lessons.created_at) = $period");
			$query = $query->groupBy(DB::raw('lessonsPeriod'))
				->join('pre_r_lessons', 'purchased_lessons.pre_r_lesson_id', '=', "pre_r_lessons.id")
				->where('pre_r_lessons.instructor_id', $instructorId);
			return $query;
		});

		$count = [];
		$this->get(['sumEarnedInPeriod', 'lessonsPeriod'])->each(function ($item) use (&$count) {
			$count[$item->lessonsPeriod] = round($item->sumEarnedInPeriod, 2);
		});
		return $count;
	}

    /**
     * @param $instructorId
     * @param $period
     * @return array
     */
    public function getCountPurchasesLessonsForPeriod($instructorId, $period = '')
	{

		// G TODO
		// ADD WHERE WITH STATUSES!

		$this->resetCriteria();
		$this->resetScope();

		$this->scopeQuery(function ($query) use ($instructorId, $period) {
			$query = $query->select(DB::raw('COUNT( DISTINCT pre_r_lessons.id) as countBookedInPeriod'));
			if ($period == '') {
				$query = $query->addSelect(DB::raw("YEAR(purchased_lessons.created_at) as lessonsPeriod"));
			} else {
				$query = $query->addSelect(DB::raw("MONTH(purchased_lessons.created_at) as lessonsPeriod"))
					->whereRaw("YEAR(purchased_lessons.created_at) = $period");
			}

			$query = $query->groupBy(DB::raw('lessonsPeriod'))
				->join('pre_r_lessons', 'purchased_lessons.pre_r_lesson_id', '=', "pre_r_lessons.id")
				->where('pre_r_lessons.instructor_id', $instructorId);
			return $query;
		});
		$count = [];
		$this->get(['countBookedInPeriod', 'lessonsPeriod'])->each(function ($item) use (&$count) {
			$count[$item->lessonsPeriod] = $item->countBookedInPeriod;
		});
		return $count;
	}

    /**
     * @param $limit
     * @return mixed
     */
    public function getHappenedLessonsPayedInEscrow($limit = null)
	{
		$escrowStatus = PurchasedLesson::STATUS_ESCROW;
		$escrowErrorStatus = PurchasedLesson::STATUS_UNABLE_ESCROW_RELEASE;
		$this->resetCriteria();
		$this->resetScope();
		$this->model = $this->model
			->select('purchased_lessons.*')
			->whereRaw(" ( purchased_lessons.status='$escrowStatus' OR purchased_lessons.status='$escrowErrorStatus' ) ")
			->orderBy('purchased_lessons.created_at', 'asc');
		if ($limit) {
            $this->model = $this->model->limit($limit);
        }

		return $this->model->get();
	}
}
