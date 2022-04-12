<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use InfyOm\Generator\Common\BaseRepository;
use DB;
use Auth;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Cookie;
use App\Criteria\InstructorClientSearchCriteria;
use App\Criteria\StudentInstructorSearchCriteria;
use App\Criteria\UserSearchCriteria;
use App\Criteria\UserStatusCriteria;
use App\Criteria\UserIdCriteria;
use App\Criteria\UserInvitedByCriteria;
use App\Criteria\UserFilterByNameCriteria;
use App\Criteria\UserFilterByInstagranHandleCriteria;
use App\Criteria\InstructorFilterByLessonPriceRangeCriteria;
use App\Criteria\OnlyOnboardedActiveMerchantInstructorsCriteria;
use App\Criteria\InstructorFilterByLocationCriteria;
use App\Criteria\UserFilterByGenreCriteria;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Log;

/**
 * Class UserRepository
 * @package App\Repositories
 * @version July 22, 2019, 3:24 pm UTC
 *
 * @method User findWithoutFail($id, $columns = ['*'])
 * @method User find($id, $columns = ['*'])
 * @method User first($columns = ['*'])
 */
class UserRepository extends BaseRepository
{
	/**
	 * @var bool
	 */
	protected $skipPresenter = true;

	public function presenter()
	{
		return "Prettus\\Repository\\Presenter\\ModelFractalPresenter";
	}

	/**
	 * @var array
	 */
	protected $fieldSearchable = [
		'email',
		'first_name',
		'last_name',
		'full_name',
		'created_at',
		'updated_at'
	];

	protected $appends = ['full_name'];


	public function presentResponse($data)
	{
		return $this->presenter->present($data);
	}

	public function forceDelete($id)
	{
		$this->find($id)->forceDelete();
	}

	/**
	 * Configure the Model
	 **/
	public function model()
	{
		return User::class;
	}

	public function getByToken(Request $request)
	{
		if (!$request->has('email') || !$request->has('token')) {
			return response([
				'errors'  => [],
				'message' => 'Email and Token required',
			], 400);
		}

		$user = $this->model()->where('email', '=', $request->email)->where('token', '=', $request->token)->first();

		return $user;
	}

	public function getAdministrators()
	{

		return $this->model
			->leftJoin("model_has_roles", 'users.id', '=', "model_has_roles.model_id")
			->where('model_has_roles.role_id', '=', 1)
			->where('model_type', '=', 'App\Models\User')
			->get();
	}


	public function getInstructorsForHome()
	{
		$this->scopeQuery(function ($query) {
			$instructorRoleId = Role::findByName(User::ROLE_INSTRUCTOR)->id;
			$query = $query->selectRaw('users.*')
				->leftJoin("model_has_roles", 'users.id', '=', "model_has_roles.model_id")
				->leftJoin("featured_instructors", 'users.id', '=', "featured_instructors.instructor_id")
				->where('featured_instructors.instructor_id', '<>', 'NULL')
				->where('model_has_roles.role_id', '=', $instructorRoleId)
				->leftJoin("profiles", 'users.id', '=', "profiles.user_id")
				->where('model_type', '=', 'App\Models\User')
				->groupBy('users.id');

			return $query;
		});

		

		$this->orderBy('featured_instructors.priority', 'desc');


		return $this->with(['profile'])->get(['users.*']);
	}

	public function getUsers(Request $request, $roleID)
	{
		$this->resetCriteria();
		$this->pushCriteria(new LimitOffsetCriteria($request));

		$invitedBy = null;

		if ($request->filled('user_id')) {
			$this->pushCriteria(new UserIdCriteria($request->get('user_id')));
		} else {
			if ($request->filled('s'))
				$this->pushCriteria(new UserSearchCriteria($request->get('s')));
			if ($request->filled('status') && $request->input('status') != 'all') {
				$this->pushCriteria(new UserStatusCriteria($request->get('status')));
			}
			if ($request->filled('invited_by')) {
				$invitedBy = (int)$request->input('invited_by');
				$this->pushCriteria(new UserInvitedByCriteria($invitedBy));
			}
		}

		$this->scopeQuery(function ($query) use ($roleID, $invitedBy) {
			$query
				->leftJoin("model_has_roles", 'users.id', '=', "model_has_roles.model_id")
				->where('model_has_roles.role_id', '=', $roleID)
				->where('model_type', '=', 'App\Models\User')
				->join('profiles', 'users.id', '=', "profiles.user_id")
				->orderBy('users.created_at', 'desc');

			if ($invitedBy > 0) {
				$query->leftJoin("invitations", 'users.id', '=', "invitations.invited_user_id");
			}

			return $query;
		});

		$defaultPerPage = 25;
		$perPage = $defaultPerPage;
		if ($roleID == 2)
			$perPage = Cookie::get('adminInstructorsPerPage', $defaultPerPage);
		elseif ($roleID == 3)
			$perPage = Cookie::get('adminStudentsPerPage', $defaultPerPage);

		$this->with(['profile', 'roles', 'genres', 'isFeatured']);
		
		if ($request->filled('limit') && $request->input('limit') > 0)
			return $this->paginate($request->input('limit'), ['users.*']);
		else
			return $this->paginate($perPage, ['users.*']);
	}

	public function getFilteredActiveInstructors(Request $request)
	{ // for frontend search
		$this->resetCriteria();
		$this->resetScope();

		$this->pushCriteria(new RequestCriteria($request));
		$this->pushCriteria(new LimitOffsetCriteria($request));

		if ($request->filled('instructor_name'))
			$this->pushCriteria(new UserFilterByNameCriteria($request->get('instructor_name')));
		if ($request->filled('instagram_handle'))
			$this->pushCriteria(new UserFilterByInstagranHandleCriteria($request->get('instagram_handle')));
		if ($request->filled('genre'))
			$this->pushCriteria(new UserFilterByGenreCriteria($request->get('genre')));
		if ($request->filled('location'))
			$this->pushCriteria(new InstructorFilterByLocationCriteria($request->get('location')));

		//        if ($request->filled('rate_from') || $request->filled('rate_to'))
		//            $this->pushCriteria(new InstructorFilterByLessonPriceRangeCriteria($request->get('rate_from', 0), $request->get('rate_to', 9999999)));

		// if (config('app.env')=='prod'){
		//     $this->pushCriteria(new OnlyOnboardedActiveMerchantInstructorsCriteria());
		// }

		$this->pushCriteria(new UserStatusCriteria(User::STATUS_ACTIVE));

		$this->scopeQuery(function ($query) use ($request) {
			$instructorRoleId = Role::findByName(User::ROLE_INSTRUCTOR)->id;
			$query = $query->selectRaw('users.*, MIN(bookings.spot_price) as min_rate, MAX(bookings.spot_price) as max_rate')
				->leftJoin("model_has_roles", 'users.id', '=', "model_has_roles.model_id")
				->leftJoin('bookings', function ($join) {
					$join->on('bookings.instructor_id', '=', 'users.id')
						->whereRaw("( bookings.status <> '" . Booking::STATUS_CANCELLED . "') AND bookings.created_at>'" . date('Y-m-d H:i:s', strtotime('-6 months')) . "'");
				})
				->where('model_has_roles.role_id', '=', $instructorRoleId)
				->leftJoin("profiles", 'users.id', '=', "profiles.user_id")
				->leftJoin("user_genre", 'users.id', '=', "user_genre.user_id")
				->where('model_type', '=', 'App\Models\User')
				->orderBy('users.created_at', 'desc')
				->groupBy('users.id');

			if ($request->filled('rate_from') || $request->filled('rate_to')) {
				// use $query as subquery
				$filterQuery = User::selectRaw('*, case when ( min_rate IS NULL ) then 0 else min_rate end as min_rate, case when ( max_rate IS NULL ) then 0 else max_rate end as max_rate ')->fromSub($query, 'users');
				$rate_from = is_numeric($request->rate_from) ? (float)$request->rate_from : 0;
				$rate_to = is_numeric($request->rate_to) ? (float)$request->rate_to : 9999999;
				if ($rate_from != 0 && $rate_to != 0) {
					if ($rate_from < $rate_to) {
						$filterQuery->whereRaw(" (min_rate >= " . $rate_from . " AND max_rate <= " . $rate_to . ") OR min_rate IS NULL ");
					} else {
						$filterQuery->whereRaw(" min_rate = " . $rate_from);
					}
				} elseif ($rate_from != 0) {
					$filterQuery->whereRaw(" (min_rate >= " . $rate_from . " OR min_rate IS NULL)");
				} elseif ($rate_to != 0) {
					$filterQuery->whereRaw(" (max_rate <= " . $rate_to . " OR max_rate IS NULL)");
				}

				if ($request->filled('orderBy') && $request->filled('sortedBy') && $request->orderBy == 'min_rate' && in_array($request->sortedBy, ['asc', 'desc'])) {
					$filterQuery->orderBy($request->orderBy, $request->sortedBy);
				}

				return $filterQuery;
			} else {
				return $query;
			}
		});

		return $this->with(['profile', 'genres'])->paginate(20, ['users.*']);
	}

	public function getInstructors(Request $request, $excludeInstructorsOfStudentUserId = null)
	{
		$this->resetCriteria();
		$this->pushCriteria(new LimitOffsetCriteria($request));

		if ($request->filled('s'))
			$this->pushCriteria(new InstructorClientSearchCriteria($request->get('s')));

		$this->scopeQuery(function ($query) use ($excludeInstructorsOfStudentUserId) {
			$instructorsIds = DB::table('instructor_client')
				->where('instructor_id', $excludeInstructorsOfStudentUserId)
				->orderBy('created_at', 'desc')
				->get()
				->pluck('client_id')
				->toArray();

			if (count($instructorsIds) > 0) {
				$query = $query->whereNotIn('users.id', $instructorsIds);
			}
			$query = $query->leftJoin("model_has_roles", 'users.id', '=', "model_has_roles.model_id")
				->where('model_has_roles.role_id', '=', 2)
				->where('model_type', '=', 'App\Models\User')
				->orderBy('users.created_at', 'desc');

			//			$query->join('profiles', 'users.id', '=', "profiles.user_id");
			return $query;
		});

		return $this->with(['profile', 'genres', 'genres.category', 'roles'])->get(['users.*']);
	}

	public function getStudents(Request $request, $excludeClientsOfInstructorUserId = null)
	{
		$this->resetCriteria();
		$this->pushCriteria(new LimitOffsetCriteria($request));

		if ($request->filled('s'))
			$this->pushCriteria(new InstructorClientSearchCriteria($request->get('s')));

		$this->scopeQuery(function ($query) use ($excludeClientsOfInstructorUserId) {
			$clientsIds = DB::table('instructor_client')
				->where('instructor_id', $excludeClientsOfInstructorUserId)
				->orderBy('created_at', 'desc')
				->get()
				->pluck('client_id')
				->toArray();

			if (count($clientsIds) > 0) {
				$query = $query->whereNotIn('users.id', $clientsIds);
			}
			$query = $query->leftJoin("model_has_roles", 'users.id', '=', "model_has_roles.model_id")
				->where('model_has_roles.role_id', '=', 3)
				->where('model_type', '=', 'App\Models\User')
				->orderBy('users.created_at', 'desc');

			$query->join('profiles', 'users.id', '=', "profiles.user_id");
			return $query;
		});

		return $this->with(['profile', 'genres', 'genres.category', 'roles'])->get(['users.*']);
	}

	public function getInstructorClients($instructorUserId = null, Request $request)
	{
		$this->resetCriteria();

		$this->pushCriteria(new LimitOffsetCriteria($request));

		if ($request->filled('s'))
			$this->pushCriteria(new InstructorClientSearchCriteria($request->get('s')));

		if (!$instructorUserId)
			$instructorUserId = Auth::user()->id;

		$this->scopeQuery(function ($query) use ($instructorUserId) {
			$clientsIds = DB::table('instructor_client')
				->where('instructor_id', $instructorUserId)
				->orderBy('created_at', 'desc')
				->get()
				->pluck('client_id')
				->toArray();
			$clientsIds[] = 0;

			$ids_ordered = implode(',', $clientsIds);
			$query = $query->whereIn('users.id', $clientsIds)
				->orderByRaw(DB::raw("FIELD(users.id, $ids_ordered)"));

			$query->join('profiles', 'users.id', '=', "profiles.user_id");
			return $query;
		});
		$perPage = Cookie::get('instructorClientsPerPage', 25);
		$this->with(['profile', 'genres', 'genres.category', 'roles']);
		if ($request->filled('limit'))
			return $this->get();
		else
			return $this->paginate($perPage, ['users.*']);
	}

	public function getStudentInstructors($studentUserId = null, Request $request)
	{
		$this->resetCriteria();

		$this->pushCriteria(new LimitOffsetCriteria($request));

		if ($request->filled('s'))
			$this->pushCriteria(new StudentInstructorSearchCriteria($request->get('s')));

		if (!$studentUserId)
			$studentUserId = Auth::user()->id;

		$this->scopeQuery(function ($query) use ($studentUserId) {
			$query->join('student_instructor', 'users.id', '=', "student_instructor.instructor_id")
				->where('student_instructor.student_id', $studentUserId)
				->orderBy('student_instructor.created_at', 'desc')
				->join('profiles', 'student_instructor.instructor_id', '=', "profiles.user_id");
			return $query;
		});

		$perPage = Cookie::get('studentInstructorsPerPage', 25);
		$this->with(['profile', 'genres', 'genres.category', 'roles']);
		$columnsArray = ['users.*', 'student_instructor.is_favorite', 'student_instructor.geo_notifications_allowed', 'student_instructor.virtual_notifications_allowed'];
		if ($request->filled('limit') && $request->input('limit') > 0)
			return $this->paginate($request->input('limit'), $columnsArray);
		else
			return $this->paginate($perPage, $columnsArray);
	}

	public function getUserData($userId)
	{
		if (!$userId)
			$userId = Auth::user()->id;
		return $this->with(['profile', 'genres', 'genres.category', 'roles'])->find($userId);
	}

	public function getByFinishRegistrationToken($token, $email)
	{
		$this->scopeQuery(function ($query) use ($token, $email) {
			$query = $query->where('finish_registration_token', $token)
				->where('email', $email)
				->where('status', User::STATUS_APPROVED);
			return $query;
		});

		return $this->first();
	}

	public function setUserSubMerchantId($user, $merchantAccountId)
	{
		$user->bt_submerchant_id = $merchantAccountId;
		$user->save();
		return false;
	}

	public function updateUserSubMerchantStatus($merchantAccountId, $status, $message = '')
	{
		$user = $this->findByField('bt_submerchant_id', $merchantAccountId)->first();
		if ($user) {
			$user->updateUserSubMerchantStatus($status, $message);
		}
		return false;
	}

	public function getStudentsWhoMayBeInterestedInRegularLesson($lesson)
	{

		$studentsWithFavoriteLessonInstructor = DB::table('student_instructor')
			->select('student_id')
			->where('geo_notifications_allowed', 1)
			->where('instructor_id', $lesson->instructor_id)
			->get()
			->pluck('student_id')
			->all();
		if (count($studentsWithFavoriteLessonInstructor) == 0)
			$studentsWithFavoriteLessonInstructor = [0];

		$this->scopeQuery(function ($query) use ($lesson, $studentsWithFavoriteLessonInstructor) {
			$rawSelect = '(';
			if ($lesson->lat && $lesson->lng) {
				$rawSelect .= " ( 
                    get_distance_in_miles_between_geo_locations({$lesson->lat},{$lesson->lng}, user_geo_locations.lat, user_geo_locations.lng) <= user_geo_locations.limit ) 
				    AND user_geo_locations.date_from <='{$lesson->start->format('Y-m-d')}' 
				    AND user_geo_locations.date_to >='{$lesson->start->format('Y-m-d')}' 
				  ) AND ";
			}

			//"(
			//    (
			//        get_distance_in_miles_between_geo_locations({$lesson->lat},{$lesson->lng}, user_geo_locations.lat, user_geo_locations.lng) <= user_geo_locations.limit
			//    )
			//    AND user_geo_locations.date_from <='{$lesson->start->format('Y-m-d')}'
			//    AND user_geo_locations.date_to >='{$lesson->start->format('Y-m-d')}'
			//) AND
			//users.id IN (".implode(',', $studentsWithFavoriteLessonInstructor).")"

			$query = $query->join('user_geo_locations', 'users.id', '=', "user_geo_locations.user_id")
				->leftJoin('user_genre', 'users.id', '=', "user_genre.user_id")
				->whereIn('users.id', $studentsWithFavoriteLessonInstructor)
				->where('users.status', '=', User::STATUS_ACTIVE)
				->where('user_genre.genre_id', '=', $lesson->genre_id)
				->groupBy('users.id');
			return $query;
		});

		return $this->all(['users.*']);
	}

	public function getStudentsWhoMayBeInterestedInVirtualLesson($lesson)
	{

		$studentsWhoWantToBeNotifiedInstructor = DB::table('student_instructor')
			->select('student_id')
			->where('virtual_notifications_allowed', 1)
			->where('instructor_id', $lesson->instructor_id)
			->get()
			->pluck('student_id')
			->all();
		if (count($studentsWhoWantToBeNotifiedInstructor) == 0)
			$studentsWhoWantToBeNotifiedInstructor = [0];

		$this->scopeQuery(function ($query) use ($lesson, $studentsWhoWantToBeNotifiedInstructor) {
			$query = $query->leftJoin('user_genre', 'users.id', '=', "user_genre.user_id")
				->whereIn('users.id', $studentsWhoWantToBeNotifiedInstructor)
				->where('users.status', '=', User::STATUS_ACTIVE)
				->where('user_genre.genre_id', '=', $lesson->genre_id)
				->groupBy('users.id');
			return $query;
		});

		return $this->all(['users.*']);
	}

	public function getAverageNewStudents()
	{
		$firstStudent = $this->model->orderBy('created_at', 'asc')->first();
		$countMonths = Carbon::now()->diffInMonths($firstStudent->created_at);
		$studentRoleId = Role::findByName(User::ROLE_STUDENT)->id;
		$totalStudents = $this->model
			->leftJoin("model_has_roles", 'users.id', '=', "model_has_roles.model_id")
			->where('model_has_roles.role_id', '=', $studentRoleId)
			->where('model_type', '=', 'App\Models\User')
			->count();
		return $countMonths > 0 ? number_format($totalStudents / $countMonths, 1) : $totalStudents;
	}

	public function appendGenres($userId, $genres)
	{
		$user = $this->find($userId);

		$user->genres()->sync(array_merge($user->genres()->pluck('id')->toArray(), $genres));

		return;
	}

	public function updateUserData($userId, Request $request)
	{
		$user = $this->find($userId);

		$user->update($request->only([
			'first_name',
			'last_name',
			'email',
		]));

		$profileParams = [
			'address',
			'city',
			'state',
			'zip',
			'dob',
			'mobile_phone',
			'about_me',
			'gender'
		];

		if ($user->hasRole(User::ROLE_STUDENT))
			$profileParams[] = 'instagram_handle';
		else
			$profileParams[] = 'lesson_block_min_price';


		$user->profile->update($request->only($profileParams));

		if ($request->has('genres')) // $currentUser->hasRole(User::ROLE_INSTRUCTOR) &&
			$user->genres()->sync($request->input('genres'));

		return;
	}

    public function getInstructorFromGenres(object $genres)
    {
        return $this->with(['profile', 'genres', 'genres.category', 'roles'])
            ->whereHas('genres', function($query) use ($genres) {
                $query->whereIn('genre_id', $genres);
            })
            ->get();
    }
}
