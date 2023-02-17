<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Criteria\StudentBookingSearchCriteria;
use App\Criteria\InstructorBookingSearchCriteria;
use App\Criteria\InstructorBookingTypeCriteria;
use App\Criteria\SpecificBookingCriteria;
use App\Criteria\StudentBookingTypeCriteria;
use App\Criteria\LessonScheduleForDayCriteria;
use App\Criteria\LessonScheduleForWeekCriteria;
use App\Criteria\LessonScheduleForMonthCriteria;
use App\Criteria\LessonInFutureCriteria;
use App\Criteria\BackendBookingSearchCriteria;
use App\Criteria\BookingFilterByAmountRangeCriteria;
use Carbon\Carbon;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class BookingRepository
 * @package App\Repositories
 * @version July 24, 2019, 2:05 pm UTC
 *
 * @method Booking findWithoutFail($id, $columns = ['*'])
 * @method Booking find($id, $columns = ['*'])
 * @method Booking first($columns = ['*'])
 */
class BookingRepository extends BaseRepository
{

    /**
     * @var array
     */
    protected $fieldSearchable = [
    ];


    /**
     * @return string
     */
    public function model()
    {
        return Booking::class;
    }


    /**
     * @var bool
     */
    protected $skipPresenter = true;

    /**
     * @return string
     */
    public function presenter() {
        return "Prettus\\Repository\\Presenter\\ModelFractalPresenter";
    }

    /**
     * @param $data
     * @return mixed
     */
    public function presentResponse($data){
        return $this->presenter->present($data);
    }

    /**
     * @param Request $request
     * @param $studentUserId
     * @return LengthAwarePaginator|Collection|mixed
     * @throws RepositoryException
     */
    public function getStudentBookings(Request $request, $studentUserId = null){
        if (!$studentUserId)
            $studentUserId = Auth::user()->id;

        $this->resetCriteria();
        $this->resetScope();

        $this->pushCriteria(new LimitOffsetCriteria($request));

        if ($request->filled('s'))
            $this->pushCriteria(new StudentBookingSearchCriteria($request->get('s')));

        $type = $request->input('type', 'current');
        $this->pushCriteria(new StudentBookingTypeCriteria($type));

        $this->pushCriteria(new RequestCriteria($request));
        if ($request->has('day'))
            $this->pushCriteria(new LessonScheduleForDayCriteria($request));
        elseif ($request->has('week'))
            $this->pushCriteria(new LessonScheduleForWeekCriteria($request));
        elseif ($request->has('month'))
            $this->pushCriteria(new LessonScheduleForMonthCriteria($request));

        $this->scopeQuery(function($query) use ($studentUserId){
            $query->join('users', 'bookings.instructor_id', '=', "users.id")
                ->join('profiles', 'users.id', '=', "profiles.user_id")
                ->join('lessons', 'bookings.lesson_id', '=', "lessons.id")
                ->join('genres', 'lessons.genre_id', '=', "genres.id")
                ->where('bookings.student_id', $studentUserId)
                ->orderBy('bookings.created_at', 'desc');
            return $query;
        });
        $perPage = Cookie::get('studentBookingsPerPage', 25);
        $this->with(['student', 'lesson', 'lesson.instructor', 'lesson.instructor.profile', 'student.profile', 'lesson.genre']);
        if ($request->filled('limit'))
            return $this->get(['bookings.*']);
        else
            return $this->paginate($perPage, ['bookings.*']);
    }

    /**
     * @param Request $request
     * @param $instructorUserId
     * @return LengthAwarePaginator|Collection|mixed
     * @throws RepositoryException
     */
    public function getInstructorBookings(Request $request, $instructorUserId = null){
        $this->resetCriteria();
        $this->resetScope();

        $this->pushCriteria(new LimitOffsetCriteria($request));

        if ($request->filled('s'))
            $this->pushCriteria(new InstructorBookingSearchCriteria($request->get('s')));
        if ($request->filled('type'))
            $this->pushCriteria(new InstructorBookingTypeCriteria($request->get('type')));
        if ($request->filled('booking'))
            $this->pushCriteria(new SpecificBookingCriteria($request->get('booking')));

        if (!$instructorUserId)
            $instructorUserId = Auth::user()->id;

        $this->scopeQuery(function($query) use ($instructorUserId){
            $query->join('users', 'bookings.instructor_id', '=', "users.id")
                ->join('profiles', 'users.id', '=', "profiles.user_id")
                ->join('lessons', 'bookings.lesson_id', '=', 'lessons.id')
                ->join('genres', 'lessons.genre_id', '=', "genres.id")
                ->where('bookings.instructor_id', $instructorUserId)
                ->orderBy('bookings.created_at', 'desc');
            return $query;
        });

        $perPage = Cookie::get('instructorBookingsPerPage', 25);
        $this->with(['instructor', 'student', 'lesson', 'instructor.profile', 'student.profile']);
        if ($request->filled('limit') && $request->input('limit')>0)
            return $this->paginate($request->input('limit'), ['bookings.*']);
        else
            return $this->paginate($perPage, ['bookings.*']);
    }

    // for backend payments list

    /**
     * @param Request $request
     * @return LengthAwarePaginator|Collection|mixed
     * @throws RepositoryException
     */
    public function getBookings(Request $request){
        $this->resetCriteria();
        $this->resetScope();

        $this->pushCriteria(new LimitOffsetCriteria($request));

        if ($request->filled('s'))
            $this->pushCriteria(new BackendBookingSearchCriteria($request->get('s')));
        if ($request->filled('price_from') || $request->filled('price_to'))
            $this->pushCriteria(new BookingFilterByAmountRangeCriteria($request->get('price_from', 0), $request->get('price_to', 9999999)));

        $this->scopeQuery(function($query) use ($request){
            $query->join('users as instructors', 'bookings.instructor_id', '=', "instructors.id")
                ->join('users as students', 'bookings.student_id', '=', "students.id")
                ->whereNotNull('transaction_id');

            if ($request->filled('order')){
                $sort = $request->input('sort', 'asc');
                if (!in_array($sort, ['asc', 'desc']))
                    $sort = 'asc';

                switch ( $request->input('order') ){
                    case 'recepient':
                        $query->orderByRaw(DB::raw("CONCAT(instructors.first_name, ' ', instructors.last_name) $sort"));
                        break;
                    case 'payer':
                        $query->orderByRaw(DB::raw("CONCAT(students.first_name, ' ', students.last_name) $sort"));
                        break;
                    case 'updated_at':
                        $query->orderBy('updated_at', $sort);
                        break;
                    case 'date':
                        $query->orderBy('transaction_created_at', $sort);
                        break;
                    case 'status':
                        $statuses_ordered = [Booking::STATUS_CANCELLED, Booking::STATUS_COMPLETE, Booking::STATUS_ESCROW, Booking::STATUS_ESCROW_RELEASED, Booking::STATUS_UNABLE_ESCROW_RELEASE];
                        if ($sort=='desc')
                            $statuses_ordered = array_reverse($statuses_ordered);
                        $statuses_ordered = "'" . implode("','" , $statuses_ordered) . "'";
                        $query->orderByRaw(DB::raw("FIELD(bookings.status, $statuses_ordered)"));
                        break;
                    default:
                        $query->orderBy('bookings.transaction_created_at', 'desc');
                }
            }

            return $query;
        });




        $perPage = Cookie::get('adminBookingsPerPage', 25);

        Log::info($this->get(['bookings.*']));

        $this->with(['instructor', 'student']);
        if ($request->filled('limit') && $request->input('limit')>0)
            return $this->paginate($request->input('limit'), ['bookings.*']);
        elseif($request->filled('limit') && $request->input('limit')==-1)
            return $this->get(['bookings.*']);
        else
            return $this->paginate($perPage, ['bookings.*']);




    }

    /**
     * @param $instructorUserId
     * @return mixed
     */
    public function getCountInstructorPendingBookings($instructorUserId = null){
        $this->resetCriteria();
        $this->resetScope();

        if (!$instructorUserId)
            $instructorUserId = Auth::user()->id;

        $nowOnServer = Carbon::now()->format('Y-m-d H:i:s'); // UTC
        return $this->model
            ->select('bookings.*')
            ->join('lessons', 'bookings.lesson_id', '=', "lessons.id")
            ->where('bookings.instructor_id', $instructorUserId)
            ->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) < lessons.start")
            ->where('bookings.status', Booking::STATUS_PENDING)
            ->count();
    }

    /**
     * @param $limit
     * @return Builder|\Illuminate\Database\Eloquent\Collection
     */
    public function getPastLessonsPendingBookings($limit = null){
        $this->resetCriteria();
        $this->resetScope();
        $nowOnServer = Carbon::now()->format('Y-m-d H:i:s'); // UTC
        $this->model = $this->model
            ->select('bookings.*')
            ->join('lessons', 'bookings.lesson_id', '=', "lessons.id")
            ->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) > lessons.start")
            ->whereIn('bookings.status', [Booking::STATUS_APPROVED, Booking::STATUS_PENDING]);
        if ($limit)
            $this->model = $this->model->limit($limit);
        return $this->model->with(['lesson'])->get();
    }

    /**
     * @param $student_id
     * @return mixed
     * @throws RepositoryException
     */
    public function getStudentUpcomingBooking($student_id = null){
        $this->resetCriteria();
        $this->resetScope();

        if (!$student_id)
            $student_id = Auth::user()->id;

        $this->pushCriteria(new LessonInFutureCriteria());

        $upcomingBooking = $this->scopeQuery(function($query){
            return $query->orderBy('start','asc')
                ->join('lessons', 'bookings.lesson_id', '=', "lessons.id")
                ->whereRaw(" ( bookings.status<>'".Booking::STATUS_CANCELLED."' ) ")
                ->limit(1);
        })
            ->with('lesson', 'lesson.instructor', 'lesson.students', 'lesson.genre', 'student')
            ->findByField('student_id', $student_id)
            ->first();

        return $upcomingBooking;
    }

    /**
     * @param $student_id
     * @return array
     */
    public function getStudentBookedGenresStatistics($student_id){
        $statistics = [];

        DB::table('bookings')
            ->select('bookings.id', 'lessons.start', 'lessons.end', 'lessons.genre_id')
            ->join('lessons', 'bookings.lesson_id', '=', "lessons.id")
            ->whereRaw(" ( bookings.status<>'".Booking::STATUS_CANCELLED."' ) ")
            ->where('student_id', $student_id)
            ->get()
            ->each(function($row) use (&$statistics){
                if (!isset($statistics[$row->genre_id]))
                    $statistics[$row->genre_id] = ['minutes' => 0, 'lessons' =>0, 'genre_id' => $row->genre_id];

                $to = Carbon::createFromFormat('Y-m-d H:i:s', $row->end);
                $from = Carbon::createFromFormat('Y-m-d H:i:s', $row->start);
                $diff_in_minutes = $to->diffInMinutes($from);

                $statistics[$row->genre_id]['lessons'] += 1;
                $statistics[$row->genre_id]['minutes'] += $diff_in_minutes;

            });

        if (count($statistics)){
            $lessonsCount = array_column($statistics, 'lessons');
            array_multisort($lessonsCount, SORT_ASC, $statistics);
            $statistics = array_reverse($statistics);
        }

        return $statistics;
    }

    /**
     * @return mixed
     */
    public function getBookingsWhereUserShouldReceiveRegularNotifications(){

        $nowOnServer = Carbon::now()->format('Y-m-d H:i:s'); // UTC

        $this->model = $this->model
            ->select('bookings.*')
            ->join('lessons', 'bookings.lesson_id', '=', "lessons.id")
            ->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) <= lessons.start")
            ->whereIn('bookings.status', [Booking::STATUS_COMPLETE, Booking::STATUS_ESCROW, Booking::STATUS_ESCROW_RELEASED]);

        return $this->model->get();
    }

    /**
     * @param $limit
     * @return Builder|\Illuminate\Database\Eloquent\Collection
     */
    public function getTooLongTimePendingBookings($limit = null){

        $timeToApprove = Setting::getValue('time_to_approve_booking', 48);

        $nowOnServer = Carbon::now()->format('Y-m-d H:i:s'); // UTC
        $this->resetCriteria();
        $this->resetScope();
        $this->model = $this->model
            ->select('bookings.*')
            ->join('lessons', 'bookings.lesson_id', '=', "lessons.id")
            ->whereRaw("bookings.created_at <= DATE_SUB('$nowOnServer', INTERVAL $timeToApprove HOUR)")
            ->where('bookings.status', Booking::STATUS_PENDING)
            ->orderBy('bookings.created_at', 'asc');
        if ($limit)
            $this->model = $this->model->limit($limit);

        return $this->model->with(['lesson'])->get();
    }

    /**
     * @param $limit
     * @return Builder|\Illuminate\Database\Eloquent\Collection
     */
    public function getHappenedLessonsPayedInEscrowBookings($limit = null){

        $timePastHappened = 8; // hours
        $nowOnServer = Carbon::now()->format('Y-m-d H:i:s'); // UTC
        $escrowStatus = Booking::STATUS_ESCROW;
        $escrowErrorStatus = Booking::STATUS_UNABLE_ESCROW_RELEASE;
        $this->resetCriteria();
        $this->resetScope();
        $this->model = $this->model
            ->select('bookings.*')
            ->join('lessons', 'bookings.lesson_id', '=', "lessons.id")
            ->whereRaw("lessons.start <= DATE_SUB(CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id), INTERVAL $timePastHappened HOUR)")
            ->whereRaw(" ( bookings.status='$escrowStatus' OR bookings.status='$escrowErrorStatus' ) ")
            ->orderBy('bookings.created_at', 'asc');
        if ($limit)
            $this->model = $this->model->limit($limit);

        return $this->model->with(['lesson'])->get();
    }

    /**
     * @param $instructorId
     * @param Request $request
     * @return LengthAwarePaginator|Collection|mixed
     */
    public function getInstructorPayouts($instructorId, Request $request){
        $this->resetCriteria();
        $this->resetScope();

        $this->scopeQuery(function($query) use ($instructorId){
            $query = $query->select(DB::raw('SUM(bookings.spot_price) as totalPayoutsAmount'), DB::raw("DATE(bookings.updated_at) as payoutsPeriod"))
                ->groupBy(DB::raw('DATE(bookings.updated_at)'))
                ->where('bookings.instructor_id', $instructorId)
                ->whereIn('bookings.status', [Booking::STATUS_COMPLETE])// , Booking::STATUS_ESCROW_RELEASED
                ->orderBy('bookings.updated_at' , 'DESC');
            return $query;
        });

        $perPage = Cookie::get('instructorPayoutsPerPage', 25);

        if ($request->filled('limit') && $request->input('limit')>0)
            return $this->paginate($request->input('limit'), ['totalPayoutsAmount', 'payoutsPeriod']);
        else
            return $this->paginate($perPage, ['totalPayoutsAmount', 'payoutsPeriod']);
    }

    /**
     * @param $instructorId
     * @param $period
     * @return array
     */
    public function getAmountEarnedForPeriod($instructorId, $period = ''){
        $this->resetCriteria();
        $this->resetScope();

        $this->scopeQuery(function($query) use ($instructorId, $period){
            $nowOnServer = Carbon::now()->format('Y-m-d H:i:s'); // UTC

            if ($period=='')
                $query = $query->select(DB::raw('SUM(bookings.spot_price) as sumEarnedInPeriod'), DB::raw("YEAR(lessons.start) as lessonsPeriod"));
            else
                $query = $query->select(DB::raw('SUM(bookings.spot_price) as sumEarnedInPeriod'), DB::raw("MONTH(lessons.start) as lessonsPeriod"))
                    ->whereRaw("YEAR(lessons.start) = $period");

            $query = $query->groupBy(DB::raw('lessonsPeriod'))
                ->join('lessons', 'bookings.lesson_id', '=', "lessons.id")
                ->where('lessons.instructor_id', $instructorId)
                ->whereRaw(" ( lessons.is_cancelled is NULL OR lessons.is_cancelled=0 ) ")
                ->whereIn('bookings.status', [Booking::STATUS_COMPLETE, Booking::STATUS_UNABLE_ESCROW_RELEASE, Booking::STATUS_ESCROW_RELEASED])
                ->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) >= lessons.start");
            return $query;
        });

        $count = [];
        $this->get(['sumEarnedInPeriod', 'lessonsPeriod'])->each(function($item) use (&$count){
            $count[$item->lessonsPeriod] = round($item->sumEarnedInPeriod, 2);
        });
        return $count;
    }


    /**
     * @param $instructorId
     * @return int|mixed
     */
    public function totalAmountInEscrow ($instructorId){
        $this->resetCriteria();
        $this->resetScope();
        $this->scopeQuery(function($query) use ($instructorId){
            $query = $query->select(DB::raw('SUM(bookings.spot_price) as totalPayoutYTD'), DB::raw("DATE(bookings.updated_at) as payoutsPeriod"))
                ->groupBy(DB::raw('DATE(bookings.updated_at)'))
                ->where('bookings.instructor_id', $instructorId)
                ->where(DB::raw('YEAR(created_at)'), '=',  now()->year)
                ->whereIn('bookings.status', [Booking::STATUS_COMPLETE])// , Booking::STATUS_ESCROW_RELEASED
                ->orderBy('bookings.updated_at' , 'DESC');
            return $query;
        });

        $total = $this->get(['totalPayoutYTD'])->sum('totalPayoutYTD');
        Log::info($total);

        return ($total) ? $total : 0;
    }

    /*
     * total amount of payments in escrow(moved to marketplace account but not yet disbursed to merchant account)
     */
    /**
     * @param $instructorId
     * @param $period
     * @return array
     */
    public function getAmountBookedForPeriod($instructorId, $period = ''){
        $this->resetCriteria();
        $this->resetScope();

        $this->scopeQuery(function($query) use ($instructorId, $period){
            $nowOnServer = Carbon::now()->format('Y-m-d H:i:s'); // UTC

            if ($period=='')
                $query = $query->select(DB::raw('SUM(bookings.spot_price) as sumEarnedInPeriod'), DB::raw("YEAR(lessons.start) as lessonsPeriod"));
            else
                $query = $query->select(DB::raw('SUM(bookings.spot_price) as sumEarnedInPeriod'), DB::raw("MONTH(lessons.start) as lessonsPeriod"))
                    ->whereRaw("YEAR(lessons.start) = $period");

            $query = $query->groupBy(DB::raw('lessonsPeriod'))
                ->join('lessons', 'bookings.lesson_id', '=', "lessons.id")
                ->where('lessons.instructor_id', $instructorId)
                ->whereRaw(" ( lessons.is_cancelled is NULL OR lessons.is_cancelled=0 ) ")
                ->whereIn('bookings.status', [Booking::STATUS_ESCROW])
                ->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) < lessons.start");
            return $query;
        });

        $count = [];
        $this->get(['sumEarnedInPeriod', 'lessonsPeriod'])->each(function($item) use (&$count){
            $count[$item->lessonsPeriod] = round($item->sumEarnedInPeriod, 2);
        });
        return $count;
    }

    /**
     * @param $transactionId
     * @return bool
     */
    public function markBookingTransactionAsComplete($transactionId){
        $booking = $this->findWhere([
            'transaction_id' => $transactionId,
            ['transaction_status', '<>', Booking::STATUS_COMPLETE]
        ])->first();
        if ($booking){
            $booking->transaction_status = 'settled';
            $booking->status = Booking::STATUS_COMPLETE;
            $booking->save();
            return true;
        }
        return false;
    }

    /**
     * @param $transactionId
     * @param $exceptionMessage
     * @param $followUpAction
     * @return bool
     */
    public function markBookingTransactionAsFailedDisbursement($transactionId, $exceptionMessage, $followUpAction){
        $booking = $this->findWhere([
            'transaction_id' => $transactionId,
            ['transaction_status', '<>', Booking::STATUS_COMPLETE]
        ])->first();
        if ($booking){
            $reason = 'Can\'t release from escrow: ' . ucfirst(str_replace('_', ' ', $exceptionMessage)) . '; Action Expected: ' . ucfirst(str_replace('_', ' ', $followUpAction));
            $booking->setStatusAttribute(Booking::STATUS_UNABLE_ESCROW_RELEASE);
            $booking->status_reason = $reason;
            $booking->save();
            Log::channel('braintree')->error('Booking #'.$booking->id.', Transaction #'.$booking->transaction_id.'; ' .$reason);
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getCountActiveStudents(){
        $this->resetCriteria();
        $this->resetScope();
        $lastDate = Carbon::now()->subDays(30)->format('Y-m-d H:i:s');
        return $this->model
            ->join('lessons', 'bookings.lesson_id', '=', "lessons.id")
            ->whereRaw("CONVERT_TZ('$lastDate', 'GMT', lessons.timezone_id) <= lessons.start")
            ->groupBy('bookings.student_id')
            ->count();
    }
}
