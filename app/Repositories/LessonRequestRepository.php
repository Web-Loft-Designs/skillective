<?php

namespace App\Repositories;

use App\Models\LessonRequest;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Criteria\LessonRequestSearchCriteria;
use Carbon\Carbon;
use Prettus\Repository\Exceptions\RepositoryException;

class LessonRequestRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'instructor_id',
        'student_id',
        'genre_id',
        'start',
        'end',
        'count_participants',
        'lesson_price',
        'location'
    ];


    /**
     * @return string
     */
    public function model()
    {
        return LessonRequest::class;
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
     * @return LengthAwarePaginator|Collection|mixed
     * @throws RepositoryException
     */
    public function getUserLessonRequests(Request $request)
    {
        $this->resetCriteria();
        $this->resetScope();

        $this->pushCriteria(new LimitOffsetCriteria($request));
        $this->pushCriteria(new RequestCriteria($request));

        if ($request->filled('s'))
            $this->pushCriteria(new LessonRequestSearchCriteria($request->get('s')));

        $this->scopeQuery(function ($query) {
            $isInstructor = Auth::user()->hasRole(User::ROLE_INSTRUCTOR);
            $userField = $isInstructor ? 'instructor_id' : 'student_id';
            $nowOnServer = Carbon::now()->format('Y-m-d H:i:s');

            $query = $query->select('lesson_requests.*')->orderBy('start', 'asc')
                ->join('genres', 'lesson_requests.genre_id', '=', "genres.id")
                ->where("lesson_requests.{$userField}", Auth::id())
                ->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lesson_requests.timezone_id) < lesson_requests.end")
                ->where(function($q) {
                    $q->where("lesson_requests.status", LessonRequest::STATUS_APPROVED)
                        ->orWhere("lesson_requests.status", LessonRequest::STATUS_PENDING);
                });

            if ($isInstructor)
                $query = $query->join('users', 'lesson_requests.student_id', '=', "users.id")
                    ->join('profiles', 'users.id', '=', "profiles.user_id");
            else
                $query = $query->join('users', 'lesson_requests.instructor_id', '=', "users.id")
                    ->join('profiles', 'users.id', '=', "profiles.user_id");
            return $query;
        })
            ->with(['genre', 'instructor.profile', 'instructor', 'student', 'student.profile', 'instructor.genres'])
            ->orderBy('created_at', 'desc');

        $perPage = Cookie::get('instructorBookingsPerPage', 20);

        if ($request->filled('limit'))
            return $this->get();
        else
            return $this->paginate($perPage);
    }

    /**
     * @param $limit
     * @return mixed
     */
    public function getTooLongTimePendingLessonRequests($limit = null)
    {
        $timeToApprove = Setting::getValue('time_to_approve_lesson_request', 24);

        $nowOnServer = Carbon::now()->format('Y-m-d H:i:s'); // UTC
        $this->resetCriteria();
        $this->resetScope();

        $this->model = $this->model->select('lesson_requests.*')
            ->whereRaw("( ( created_at <= DATE_SUB('$nowOnServer', INTERVAL $timeToApprove HOUR) ) OR lesson_requests.start <= CONVERT_TZ('$nowOnServer', 'GMT', lesson_requests.timezone_id) )")
            ->where('status', LessonRequest::STATUS_PENDING)
            ->orWhere('status', LessonRequest::STATUS_APPROVED)
            ->orderBy('created_at', 'asc');
        if ($limit)
            $this->model = $this->model->limit($limit);

        return $this->model->get();
    }
}
