<?php
namespace App\Services;

use App\Repositories\BookingRepository;
use App\Repositories\UserRepository;
use App\Repositories\LessonRepository;
use App\Repositories\InvitationRepository;
use App\Repositories\GenreRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Exceptions\RepositoryException;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Exceptions\InvalidPeriod;
use Spatie\Permission\Models\Role;
use App\Models\Booking;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Setting;
use Spatie\Analytics\Period;

class ReportsBuilder {
	/*
	 * @var BookingRepository $booking_repository
	 */
	private $booking_repository = null;

	/*
	 * @var UserRepository $user_repository
	 */
	private $user_repository = null;

	/*
	 * @var LessonRepository $lesson_repository
	 */
	private $lesson_repository = null;

	/*
	 * @var InvitationRepository $invitation_repository
	 */
	private $invitation_repository = null;




	public function __construct(BookingRepository $booking_repository, UserRepository $user_repository, LessonRepository $lesson_repository, InvitationRepository $invitation_repository, GenreRepository $genre_repository)
	{
		$this->booking_repository = $booking_repository;
		$this->user_repository = $user_repository;
		$this->lesson_repository = $lesson_repository;
		$this->invitation_repository = $invitation_repository;
		$this->genre_repository = $genre_repository;
	}

    /**
     * @return array
     */
    public function getDemographicReportData(){
		$data = [
			'< 20' => ['count' => 0 , 'percent' => 0],
			'21 - 30' => ['count' => 0 , 'percent' => 0],
			'31 - 40' => ['count' => 0 , 'percent' => 0],
			'41 - 50' => ['count' => 0 , 'percent' => 0],
			'51 - 60' => ['count' => 0 , 'percent' => 0],
			'> 60' => ['count' => 0 , 'percent' => 0],
		];
		$countAll = 0;

		$studentRoleId = Role::findByName(User::ROLE_STUDENT)->id;

		$ages = DB::table('profiles')
				  ->select(DB::raw('YEAR(CURDATE()) - YEAR(profiles.dob) AS age'))
				  ->leftJoin('users', 'profiles.user_id', '=', 'users.id')
				  ->leftJoin("model_has_roles", 'users.id', '=', "model_has_roles.model_id")
				  ->where('model_has_roles.role_id', '=', $studentRoleId)
				  ->where('model_type', '=', 'App\Models\User')
				  ->whereNotNull('profiles.dob')
				  ->get();
		foreach ($ages as $row){
			$countAll++;
			if ($row->age<21){
				$data['< 20']['count']++;
			}elseif ($row->age>20 && $row->age<31){
				$data['21 - 30']['count']++;
			}elseif ($row->age>30 && $row->age<41){
				$data['31 - 40']['count']++;
			}elseif ($row->age>40 && $row->age<51){
				$data['41 - 50']['count']++;
			}elseif ($row->age>50 && $row->age<61){
				$data['51 - 60']['count']++;
			}elseif ($row->age>60) {
				$data['> 60']['count']++;
			}
		}

		foreach ($data as $k=>$ageGroup){
			$data[$k]['percent'] = $countAll>0 ? round($data[$k]['count'] / $countAll * 100, 1) : 0;
		}
		$return = [];
		foreach ($data as $k=>$ageGroup){
			$r = $ageGroup;
			$r['range'] = $k;
			$return[] = $r;

		}

		return $return;
	}

    /**
     * @return array
     */
    public function getGeographicReportData(){
		$states = getStatesAssociativeArray();
		$data = [];
		$countAll = 0;
		$countInStates = DB::table('profiles')
				  ->select(DB::raw('COUNT(profiles.state) as countInState'), 'profiles.state')
				  ->whereNotNull('profiles.state')
				  ->groupBy('profiles.state')
				  ->get();

		foreach ($states as $key => $title){
			$data[$key] = [
				'state' => $key,
				'count' => 0,
				'percent' => 0,
				'stateName' => $title
			];
		}

		foreach ($countInStates as $row){
			$data[$row->state] = [
				'state' => $row->state,
				'count' => $row->countInState,
				'percent' => 0,
				'stateName' => isset($states[$row->state])? $states[$row->state] : $row->state
			];
			$countAll += $row->countInState;
		}

		foreach ($data as $k=>$ageGroup){
			$data[$k]['percent'] = $countAll>0 ? number_format( ($data[$k]['count'] / $countAll * 100), 2) : 0;
		}

		return array_values($data);
	}

    /**
     * @return array
     * @throws RepositoryException
     */
    public function getOtherReportsData(){
		$data = [
			['paramName' => '% of transactions not completed', 'value' => $this->getNotCompletedTransactionsPercent()],
			['paramName' => 'Search Queries by Genre', 'value' => $this->getCountSearchQueriesByGenre()],
			['paramName' => 'Search Queries by Location', 'value' => $this->getCountSearchQueriesByLocation()],
			['paramName' => 'Lessons Cancelled %', 'value' => $this->getCountCancelledLessons()],
			['paramName' => 'Active Instructors', 'value' => $this->getCountActiveInstructors()],
			['paramName' => 'Active Client', 'value' => $this->getCountActiveClients()],
			['paramName' => 'Instructors invited / month', 'value' => $this->getAverageInvitedInstructors()],
			['paramName' => 'Clients added / month', 'value' => $this->getAverageNewStudents()],
			['paramName' => 'Genre Analysis', 'value' => ''], // Number of lessons for each genre & % of the total lessons
		];

		$totalLessons = $this->lesson_repository->countFutureLessons();
		$this->lesson_repository->getAverageLessonsPerGenre()->each(function($lessonData) use (&$data, $totalLessons){
			$data[] = [
				'paramName' => $lessonData->genre_title . ' %',
				'value' => $totalLessons>0 ? number_format($lessonData->count_lessons / $totalLessons * 100, 1) : 0
			];
		});
		return $data;
	}

    /**
     * @param $selectedPeriod
     * @return array
     */
    public function getOverview($selectedPeriod = null){
		if ($selectedPeriod==null)
			$selectedPeriod = '-30 days';

		$widgetData = [];
		if (strtotime($selectedPeriod)===false)
			return $widgetData;

		$selectedPeriodStart = strtotime($selectedPeriod);
		$selectedPeriodEnd = time();
		$previousPeriodStart = $selectedPeriodEnd - 2 * ($selectedPeriodEnd - strtotime($selectedPeriod));

		$selectedPeriodStart = date('Y-m-d H:i:s', $selectedPeriodStart);
		$selectedPeriodEnd = date('Y-m-d H:i:s', $selectedPeriodEnd);
		$previousPeriodStart = date('Y-m-d H:i:s', $previousPeriodStart);

		$countLessonsInSelectedPeriod = $this->getCountLessonsInPeriod($selectedPeriodStart, $selectedPeriodEnd);
		$countLessonsInPreviousPeriod = $this->getCountLessonsInPeriod($previousPeriodStart, $selectedPeriodStart);
		$countLessonsDiff = getPercentDiff($countLessonsInSelectedPeriod, $countLessonsInPreviousPeriod);

		$widgetData[] = [
			'paramName' => 'Lessons',
			'currentValue' => $countLessonsInSelectedPeriod,
			'prevValue' => $countLessonsInPreviousPeriod,
			'diff' => $countLessonsDiff
		];

		$studentRoleId = Role::findByName(User::ROLE_STUDENT)->id;
		$countNewStudentsInSelectedPeriod = $this->getCountNewUsersInPeriod($selectedPeriodStart, $selectedPeriodEnd, $studentRoleId);
		$countNewStudentsInPreviousPeriod = $this->getCountNewUsersInPeriod($previousPeriodStart, $selectedPeriodStart, $studentRoleId);
		$countNewStudentsDiff = getPercentDiff($countNewStudentsInSelectedPeriod, $countNewStudentsInPreviousPeriod);

		$widgetData[] = [
			'paramName' => 'Users',
			'currentValue' => $countNewStudentsInSelectedPeriod,
			'prevValue' => $countNewStudentsInPreviousPeriod,
			'diff' => $countNewStudentsDiff
		];

		$instructorRoleId = Role::findByName(User::ROLE_INSTRUCTOR)->id;
		$countNewInstructorsInSelectedPeriod = $this->getCountNewUsersInPeriod($selectedPeriodStart, $selectedPeriodEnd, $instructorRoleId);
		$countNewInstructorsInPreviousPeriod = $this->getCountNewUsersInPeriod($previousPeriodStart, $selectedPeriodStart, $instructorRoleId);
		$countNewInstructorsDiff = getPercentDiff($countNewInstructorsInSelectedPeriod, $countNewInstructorsInPreviousPeriod);

		$widgetData[] = [
			'paramName' => 'Instructors',
			'currentValue' => $countNewInstructorsInSelectedPeriod,
			'prevValue' => $countNewInstructorsInPreviousPeriod,
			'diff' => $countNewInstructorsDiff
		];

		$paymentsAmountInSelectedPeriod = $this->getPaymentsAmountInPeriod($selectedPeriodStart, $selectedPeriodEnd);
		$paymentsAmountInPreviousPeriod = $this->getPaymentsAmountInPeriod($previousPeriodStart, $selectedPeriodStart);
		$paymentsAmountDiff = getPercentDiff($paymentsAmountInSelectedPeriod, $paymentsAmountInPreviousPeriod);

		$widgetData[] = [
			'paramName' => 'Payments',
			'currentValue' => number_format($paymentsAmountInSelectedPeriod, 2, '.', ''),
			'prevValue' => number_format($paymentsAmountInPreviousPeriod, 2, '.', ''),
			'diff' => $paymentsAmountDiff
		];

        $countVisitsInSelectedPeriod = ReportsBuilder::getCountVisitsInPeriod($selectedPeriodStart, $selectedPeriodEnd);
		$countVisitsInPreviousPeriod = ReportsBuilder::getCountVisitsInPeriod($previousPeriodStart, $selectedPeriodStart);
		$countVisitsDiff = getPercentDiff($countVisitsInSelectedPeriod, $countVisitsInPreviousPeriod);

		$widgetData[] = [
			'paramName' => 'Visits',
			'currentValue' => $countVisitsInSelectedPeriod,
			'prevValue' => $countVisitsInPreviousPeriod,
			'diff' => $countVisitsDiff
		];

		return $widgetData;
	}

    /**
     * @param $periodStart
     * @param $periodEnd
     * @return int
     */
    public function getCountLessonsInPeriod($periodStart, $periodEnd){
		$nowOnServer = Carbon::now()->format('Y-m-d H:i:s'); // UTC
		return DB::table('lessons')
		  ->whereRaw(" ( lessons.is_cancelled is NULL OR lessons.is_cancelled=0 ) ")
		  ->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) >= lessons.start")
		  ->whereRaw( "lessons.start BETWEEN '" . $periodStart . "' AND '" . $periodEnd . "'")
		  ->count();
	}

    /**
     * @param $periodStart
     * @param $periodEnd
     * @param $roleId
     * @return int
     */
    public function getCountNewUsersInPeriod($periodStart, $periodEnd, $roleId){
		return DB::table('users')
			->leftJoin("model_has_roles", 'users.id', '=', "model_has_roles.model_id")
			->where('model_has_roles.role_id', '=', $roleId)
			->where('model_type', '=', 'App\Models\User')
			 ->whereRaw( "users.created_at BETWEEN '" . $periodStart . "' AND '" . $periodEnd . "'")
			 ->count();
	}

    /**
     * @param $periodStart
     * @param $periodEnd
     * @return int|mixed
     */
    public function getPaymentsAmountInPeriod($periodStart, $periodEnd){
		return DB::table('bookings')
				 ->whereNotNull('bookings.transaction_id')
				 ->whereNotIn('bookings.status', [Booking::STATUS_CANCELLED, Booking::STATUS_PENDING])
				 ->whereRaw( "bookings.transaction_created_at BETWEEN '" . $periodStart . "' AND '" . $periodEnd . "'")
				 ->sum(DB::raw('bookings.spot_price - bookings.service_fee - bookings.processor_fee - bookings.virtual_fee'));
	}

    /**
     * @param $periodStart
     * @param $periodEnd
     * @return int
     * @throws InvalidPeriod
     */
    public function getCountVisitsInPeriod($periodStart, $periodEnd){
		$periodStart = new \DateTime($periodStart);
		$periodEnd = new \DateTime($periodEnd);

		$period = new Period($periodStart, $periodEnd);
		$response = Analytics::performQuery(
			$period,
			'ga:users'
		);
		$countUsers = isset($response['rows']) ? $response['rows'][0][0] : 0;

		return $countUsers;
	}

    /**
     * @return int|string
     */
    public function getNotCompletedTransactionsPercent(){
		$countBookingFormViews = Setting::getValue('report_count_payment_form_views', 0, false);
		$countBookings = Booking::count();

		return $countBookingFormViews>0 ? number_format((100 - $countBookings / $countBookingFormViews * 100), 2) : 0;
	}

    /**
     * @return string
     */
    public function getCountSearchQueriesByGenre(){
		$countSearchesByGenre = Setting::getValue('report_count_searches_by_genre', 0, false);
		$totalSearches = Setting::getValue('report_total_searches', 0, false);

		return "{$countSearchesByGenre} / " . ($totalSearches>0 ? number_format(($countSearchesByGenre / $totalSearches * 100), 2) : 0) . '%';
	}

    /**
     * @return string
     */
    public function getCountSearchQueriesByLocation(){
		$countSearchesByLocation = Setting::getValue('report_count_searches_by_location', 0, false);
		$totalSearches = Setting::getValue('report_total_searches', 0, false);

		return "{$countSearchesByLocation} / " . ($totalSearches>0 ? number_format(($countSearchesByLocation / $totalSearches * 100), 2) : 0) . '%';
	}

    /**
     * @return int|string
     */
    public function getCountCancelledLessons(){
		$countAllLessons = Lesson::count();
		$countCancelledLessons = Lesson::where('is_cancelled', 1)->count();

		return $countAllLessons>0 ? number_format(($countCancelledLessons / $countAllLessons * 100), 2) : 0;
	}

    /**
     * @return string
     * @throws RepositoryException
     */
    public function getCountActiveInstructors(){

		$instructorRoleId = Role::findByName(User::ROLE_INSTRUCTOR)->id;
		$countAllInstructors = DB::table('model_has_roles')
					   ->where('model_has_roles.role_id', '=', $instructorRoleId)
					   ->where('model_type', '=', 'App\Models\User')
					   ->count();

		$countInstructorsHavingNotFinishedLessons = $this->lesson_repository->countActiveInstructors();//$this->lesson_repository->countInstructorsOfFutureLessons();

		return $countAllInstructors>0 ? "$countInstructorsHavingNotFinishedLessons / " . number_format(($countInstructorsHavingNotFinishedLessons / $countAllInstructors * 100), 2) . '%' : '0 / 0';
	}

    /**
     * @return string
     */
    public function getCountActiveClients(){
		$studentRoleId = Role::findByName(User::ROLE_STUDENT)->id;
		$countAllStudents = DB::table('model_has_roles')
								 ->where('model_has_roles.role_id', '=', $studentRoleId)
								 ->where('model_type', '=', 'App\Models\User')
								 ->count();

		$countActiveStudents = $this->booking_repository->getCountActiveStudents();

		return $countAllStudents>0 ? "$countActiveStudents / " . number_format(($countActiveStudents / $countAllStudents * 100), 2) . '%' : '0 / 0';
	}

    /**
     * @return int|mixed
     * @throws RepositoryException
     */
    public function getCountActiveUsers(){

		$countActiveStudents = $this->booking_repository->getCountActiveStudents();
		$countActiveInstructors = $this->lesson_repository->countActiveInstructors();//$this->lesson_repository->countInstructorsOfFutureLessons();

		return $countActiveStudents + $countActiveInstructors;
	}

    /**
     * @return string
     */
    public function getAverageInvitedInstructors(){
		return $this->invitation_repository->getAverageInvitedInstructors();
	}

    /**
     * @return string
     */
    public function getAverageNewStudents(){
		return $this->user_repository->getAverageNewStudents();
	}
}
