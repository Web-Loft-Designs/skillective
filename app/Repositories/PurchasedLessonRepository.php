<?php

namespace App\Repositories;

use InfyOm\Generator\Common\BaseRepository;
use App\Models\PurchasedLesson;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use DB;

class PurchasedLessonRepository extends BaseRepository
{

	public function model()
	{
		return PurchasedLesson::class;
	}

	protected $skipPresenter = true;

	public function presenter()
	{
		return "Prettus\\Repository\\Presenter\\ModelFractalPresenter";
	}

	public function presentResponse($data)
	{
		return $this->presenter->present($data);
	}


	public function getStudentPurchasedLessons(Request $request, $studentUserId = null)
	{
		if (!$studentUserId)
			$studentUserId = Auth::user()->id;

		$this->resetCriteria();
		$this->resetScope();

		$this->scopeQuery(function ($query) use ($studentUserId, $request) {
			$query->join('users', 'purchased_lessons.student_id', '=', "users.id")
				->join('profiles', 'users.id', '=', "profiles.user_id")
				->join('pre_r_lessons', 'purchased_lessons.pre_r_lesson_id', '=', "pre_r_lessons.id")
				->join('genres', 'pre_r_lessons.genre_id', '=', "genres.id")
				->where('purchased_lessons.student_id', $studentUserId)
				->orderBy('purchased_lessons.created_at', 'desc');

            if( $request->has('genre') )
            {
                $query->where('pre_r_lessons.genre_id', $request->input('genre'));
            }

			return $query;
		});

		$this->with(['student', 'preRecordedLesson', 'preRecordedLesson.instructor', 'preRecordedLesson.instructor.profile', 'student.profile', 'preRecordedLesson.genre', 'preRecordedLesson.files']);

		if ($request->filled('limit'))
			return $this->get(['purchased_lessons.*']);
		else
			return $this->paginate(25, ['purchased_lessons.*']);
	}

	public function getAmountEarnedForPeriod($instructorId, $period = '')
	{

		// G TODO
		// ADD WHERE WITH STATUSES!

		$this->resetCriteria();
		$this->resetScope();

		$this->scopeQuery(function ($query) use ($instructorId, $period) {

			if ($period == '')
				$query = $query->select(DB::raw('SUM(purchased_lessons.price) as sumEarnedInPeriod'), DB::raw("YEAR(purchased_lessons.created_at) as lessonsPeriod"));
			else
				$query = $query->select(DB::raw('SUM(purchased_lessons.price) as sumEarnedInPeriod'), DB::raw("MONTH(purchased_lessons.created_at) as lessonsPeriod"))
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

	public function getHappenedLessonsPayedInEscrow($limit = null)
	{
		// TODO

		$timePastHappened = 8; // hours

		$nowOnServer = Carbon::now()->format('Y-m-d H:i:s'); // UTC

		$escrowStatus = PurchasedLesson::STATUS_ESCROW;
		$escrowErrorStatus = PurchasedLesson::STATUS_UNABLE_ESCROW_RELEASE;

		$this->resetCriteria();
		$this->resetScope();
		$this->model = $this->model
			->select('purchased_lessons.*')
			// ->whereRaw("purchased_lessons.created_at <= DATE_SUB($nowOnServer), INTERVAL $timePastHappened HOUR)")
			->whereRaw(" ( purchased_lessons.status='$escrowStatus' OR purchased_lessons.status='$escrowErrorStatus' ) ")
			->orderBy('purchased_lessons.created_at', 'asc');
		if ($limit)
			$this->model = $this->model->limit($limit);

		return $this->model->get();
	}
}
