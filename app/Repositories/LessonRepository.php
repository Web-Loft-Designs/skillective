<?php

namespace App\Repositories;

use App\Models\Lesson;
use App\Models\Booking;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserGeoLocation;
use Braintree\MerchantAccount;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Criteria\LessonFilterByGenreCriteria;
use App\Criteria\LessonFilterByLocationCriteria;
use App\Criteria\LessonFilterByTimezoneOffsetCriteria;
use App\Criteria\LessonFilterByInstructorNameCriteria;
use App\Criteria\LessonFilterByLessonTypeCriteria;
use App\Criteria\LessonFilterByInstagranHandleCriteria;
use App\Criteria\LessonFilterByDateRangeCriteria;
use App\Criteria\LessonFilterByTimeRangeCriteria;
use App\Criteria\LessonFilterByPriceRangeCriteria;
use App\Criteria\LessonScheduleForDayCriteria;
use App\Criteria\LessonScheduleForWeekCriteria;
use App\Criteria\LessonScheduleForMonthCriteria;
use App\Criteria\LessonFilterByFlexCriteria;
use App\Criteria\InstructorLessonDashboardTypeCriteria;
use App\Criteria\LessonSearchCriteria;
use App\Criteria\LessonOnlyPublic;
use App\Criteria\LessonTypeCriteria;
use App\Criteria\LessonInFutureCriteria;
use App\Criteria\LessonOfOnboardedActiveMerchantInstructorsCriteria;
use App\Criteria\LessonOfActiveInstructorsCriteria;
use Carbon\Carbon;
use Prettus\Repository\Exceptions\RepositoryException;
use Torann\GeoIP\Location;

/**
 * Class LessonRepository
 * @package App\Repositories
 * @version July 24, 2019, 2:05 pm UTC
 *
 * @method Lesson findWithoutFail($id, $columns = ['*'])
 * @method Lesson find($id, $columns = ['*'])
 * @method Lesson first($columns = ['*'])
 */
class LessonRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'instructor_id',
        'genre_id',
        'start',
        'end',
        'spots_count',
        'spot_price',
        'location'
    ];


    /**
     * @return string
     */
    public function model()
    {
        return Lesson::class;
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
    public function getFilteredAvailableLessons(Request $request)
    {
        $this->resetCriteria();
        $this->resetScope();

        $this->pushCriteria(new RequestCriteria($request));
        $this->pushCriteria(new LimitOffsetCriteria($request));

        if($request->filled('instructor_name'))
            $this->pushCriteria(new LessonFilterByInstructorNameCriteria($request->get('instructor_name')));
        if($request->filled('genre'))
            $this->pushCriteria(new LessonFilterByGenreCriteria($request->get('genre')));

        if($request->filled('lesson_type'))
            $this->pushCriteria(new LessonFilterByLessonTypeCriteria($request->get('lesson_type')));
        if(!$request->filled('lesson_type') || $request->input('lesson_type') == 'in_person') {
            if($request->filled('location'))
                $this->pushCriteria(new LessonFilterByLocationCriteria($request->get('location')));
        } elseif($request->filled('lesson_type') || $request->input('lesson_type') == 'virtual') {
            if($request->filled('timezone_id') && ($time = new \DateTime('now', new \DateTimeZone($request->input('timezone_id')))) != false) {
                $this->pushCriteria(new LessonFilterByTimezoneOffsetCriteria($time->format('P')));
            }
        }
        if($request->filled('instagram_handle'))
            $this->pushCriteria(new LessonFilterByInstagranHandleCriteria($request->get('instagram_handle')));
        if($request->filled('date_from') || $request->filled('date_to'))
            $this->pushCriteria(new LessonFilterByDateRangeCriteria($request->get('date_from'), $request->get('date_to')));
        if($request->filled('time_from') || $request->filled('time_to'))
            $this->pushCriteria(new LessonFilterByTimeRangeCriteria($request->get('time_from'), $request->get('time_to')));
        if($request->filled('price_from') || $request->filled('price_to'))
            $this->pushCriteria(new LessonFilterByPriceRangeCriteria($request->get('price_from', 0), $request->get('price_to', 9999999)));

        if($request->filled('flexible_months'))
            $this->pushCriteria(new LessonFilterByFlexCriteria($request->get('flexible_months'), $request->get('flexible_days')));


        if(config('app.env') == 'prod') {
            $this->pushCriteria(new LessonOfOnboardedActiveMerchantInstructorsCriteria());
        }
        $this->pushCriteria(new LessonOfActiveInstructorsCriteria());
        $this->pushCriteria(new LessonOnlyPublic());

        $this->pushCriteria(new LessonInFutureCriteria());

        $this->scopeQuery(function($query) use ($request) {
            // apply all criterias first
            $query = $query->select('lessons.*', DB::raw("SUM(case when ( bookings.id IS NULL ) then 0 else 1 end) as count_booked"))
                ->leftJoin('bookings', function($join) {
                    $join->on('lessons.id', '=', 'bookings.lesson_id')
                        ->whereRaw(" ( bookings.status <> 'cancelled' OR bookings.status IS NULL ) ");
                })
                ->join('users', 'lessons.instructor_id', '=', "users.id")
                ->join('profiles', 'users.id', '=', "profiles.user_id")
                ->groupBy('lessons.id');

            // use $query as subquery
            $nowOnServer = Carbon::now()->format('Y-m-d H:i:s'); // UTC

            $filterQuery = Lesson::selectRaw('lessons.*')->fromSub($query, 'lessons')
                ->whereRaw('lessons.count_booked < lessons.spots_count')
                ->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) <= lessons.start");

            if(!$request->filled('orderBy') || !$request->filled('sortedBy')) {
                $filterQuery->orderBy('start', 'asc');
            }

            return $filterQuery;
        });

        return $this->with(['genre', 'instructor', 'instructor.profile', 'students'])->paginate(20, ['lessons.*']);
    }

    /**
     * @param $instructorId
     * @return int
     */
    public function getInstructorsPastBookedLessonsCount($instructorId)
    {
        $this->resetCriteria();
        $this->resetScope();

        $this->scopeQuery(function($query) use ($instructorId) {
            $nowOnServer = Carbon::now()->format('Y-m-d H:i:s'); // UTC
            $query = $query->select('lessons.id')
                ->groupBy('lessons.id')
                ->where('lessons.instructor_id', $instructorId)
                ->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) >= lessons.start")  // past lessons
                ->whereRaw(" ( lessons.is_cancelled is NULL OR lessons.is_cancelled=0 ) ")
                ->join('bookings', function($join) {
                    $join->on('lessons.id', '=', 'bookings.lesson_id')
                        ->whereRaw(" ( bookings.status IN ('" . Booking::STATUS_COMPLETE . "','" . Booking::STATUS_ESCROW_RELEASED . "','" . Booking::STATUS_ESCROW . "','" . Booking::STATUS_UNABLE_ESCROW_RELEASE . "') ) ");
                });
            return $query;
        });
        return $this->get()->count();
    }

    // for admins

    /**
     * @param Request $request
     * @return LengthAwarePaginator|Collection|mixed
     * @throws RepositoryException
     */
    public function getLessons(Request $request)
    {
        $this->resetCriteria();
        $this->resetScope();
        $this->pushCriteria(new LimitOffsetCriteria($request));

        if($request->filled('s'))
            $this->pushCriteria(new LessonSearchCriteria($request->get('s')));

        $instructorId = null;
        if($request->filled('instructor'))
            $instructorId = $request->input('instructor');

        $type = $request->input('type', 'current');
        $this->pushCriteria(new LessonTypeCriteria($type));

        $this->scopeQuery(function($query) use ($instructorId, $type) {
            $query = $query
                ->select('lessons.*', DB::raw("SUM(case when ( bookings.id IS NULL ) then 0 else 1 end) as count_booked"))
                ->join('users', 'lessons.instructor_id', '=', "users.id")
                ->join('profiles', 'users.id', '=', "profiles.user_id")
                ->join('genres', 'lessons.genre_id', '=', "genres.id")
                ->groupBy('lessons.id');

            if($instructorId)
                $query = $query->where('lessons.instructor_id', $instructorId);
            if($type == 'past')
                $query = $query->join('bookings', function($join) {
                    $join->on('lessons.id', '=', 'bookings.lesson_id')
                        ->whereRaw(" ( bookings.status <> 'cancelled' OR bookings.status IS NULL ) ");
                });
            else
                $query = $query->leftJoin('bookings', function($join) {
                    $join->on('lessons.id', '=', 'bookings.lesson_id')
                        ->whereRaw(" ( bookings.status <> 'cancelled' OR bookings.status IS NULL ) ");
                });

            return $query;
        })
            ->with(['genre', 'instructor.profile', 'instructor', 'students']);

        $perPage = Cookie::get('adminLessonsPerPage', 25);
        if($request->filled('limit') || $request->has('day') || $request->has('week') || $request->has('month'))
            return $this->get(['lessons.*', 'count_booked']);
        else
            return $this->paginate($perPage, ['lessons.*', 'count_booked']);
    }

    /**
     * @param Request $request
     * @param $instructor_id
     * @return LengthAwarePaginator|Collection|mixed
     * @throws RepositoryException
     */
    public function getInstructorLessons(Request $request, $instructor_id = null)
    {
        $this->resetCriteria();
        $this->resetScope();
        if(!$instructor_id)
            $instructor_id = Auth::user()->id;
        $this->pushCriteria(new LimitOffsetCriteria($request));
        $this->pushCriteria(new RequestCriteria($request));
        if($request->has('day'))
            $this->pushCriteria(new LessonScheduleForDayCriteria($request));
        elseif($request->has('week'))
            $this->pushCriteria(new LessonScheduleForWeekCriteria($request));
        elseif($request->has('month'))
            $this->pushCriteria(new LessonScheduleForMonthCriteria($request));
        $this->scopeQuery(function($query) use ($instructor_id) {
            $query = $query->orderBy('start', 'asc')
                ->select('lessons.*', DB::raw("SUM(case when ( bookings.id IS NULL ) then 0 else 1 end) as count_booked"))
                ->leftJoin('bookings', function($join) {
                    $join->on('lessons.id', '=', 'bookings.lesson_id')
                        ->whereRaw(" ( bookings.status <> 'cancelled' OR bookings.status IS NULL ) ");
                })
                ->groupBy('lessons.id')
                ->whereRaw(" ( lessons.is_cancelled is NULL OR lessons.is_cancelled=0 ) ")
                ->where('lessons.instructor_id', $instructor_id);

            return $query;
        })
            ->with(['genre', 'instructor.profile', 'instructor', 'students']);

        $perPage = Cookie::get('instructorLessonsPerPage', 20);

        if($request->filled('limit') || $request->has('day') || $request->has('week') || $request->has('month'))
            return $this->get(['lessons.*', 'count_booked']);
        else
            return $this->paginate($perPage, ['lessons.*', 'count_booked']);
    }

    /**
     * @param Request $request
     * @param $instructorUserId
     * @return LengthAwarePaginator|Collection|mixed
     * @throws RepositoryException
     */
    public function getDashboardInstructorLessons(Request $request, $instructorUserId = null)
    {
        $this->resetCriteria();
        $this->resetScope();
        $this->pushCriteria(new LimitOffsetCriteria($request));
        $this->pushCriteria(new InstructorLessonDashboardTypeCriteria($request->get('type')));
        if(!$instructorUserId)
            $instructorUserId = Auth::user()->id;
        $this->scopeQuery(function($query) use ($instructorUserId) {
            $query->where('lessons.instructor_id', '=', $instructorUserId)
                ->whereRaw(" ( lessons.is_cancelled is NULL OR lessons.is_cancelled=0 ) ")
                ->join('users', 'lessons.instructor_id', '=', "users.id")
                ->join('profiles', 'users.id', '=', "profiles.user_id")
                ->join('genres', 'lessons.genre_id', '=', "genres.id")
                ->leftJoin('bookings', 'lessons.id', '=', 'bookings.lesson_id')
                ->withCount(['bookings' => function($sub_query) {
                    $sub_query->where('status', '<>', 'cancelled');
                }]);

            return $query;
        });

        if($request->get('type') && $request->get('type') === 'past') {
            $this->orderBy('lessons.start', 'desc');
        }

        if($request->get('sort')) {
            if($request->get('sort') === 'date') {
                $this->orderBy('lessons.start', 'asc');
            } else if($request->get('sort') === 'price_asc') {
                $this->orderBy('lessons.spot_price', 'asc');
            } else if($request->get('sort') === 'price_desc') {
                $this->orderBy('lessons.spot_price', 'desc');
            } else if($request->get('sort') === 'participants') {
                $this->orderBy('bookings_count', 'desc');
            }
        }

        $this->with(['bookings', 'bookings.instructor', 'bookings.student', 'genre']);

        if($request->filled('limit') && $request->input('limit') > 0)
            return $this->paginate($request->input('limit'), ['lessons.*'])->unique('id');
        else
            return $this->paginate($perPage, ['lessons.*']); // TODO Bag !!!!!!!!!!!
    }

    /**
     * @param $instructor_id
     * @return Lesson
     * @throws RepositoryException
     */
    public function getInstructorUpcomingLesson($instructor_id = null)
    {
        $this->resetCriteria();
        $this->resetScope();
        if(!$instructor_id)
            $instructor_id = Auth::user()->id;
        $this->pushCriteria(new LessonInFutureCriteria());
        if(!Auth::user() || $instructor_id != Auth::user()->id)
            $this->pushCriteria(new LessonOnlyPublic());
        $upcomingLesson = $this->scopeQuery(function($query) use ($instructor_id) {
            return $query
                ->leftJoin('bookings', function($join) {
                    $join->on('lessons.id', '=', 'bookings.lesson_id')
                        ->whereNotIn('bookings.status', [Booking::STATUS_CANCELLED, Booking::STATUS_PENDING]);
                })
                ->orderBy('lessons.start', 'asc')
                ->whereNotNull('bookings.id')
                ->where('lessons.instructor_id', $instructor_id)
                ->limit(1);
        })
            ->with('genre', 'students')
            ->first();

        return $upcomingLesson;
    }

    /**
     * @param $instructor_id
     * @return array
     */
    public function getUpcomingInstructorLocations($instructor_id = null)
    {
        $this->resetCriteria();
        $this->resetScope();
        if(!$instructor_id)
            $instructor_id = Auth::user()->id;
        $nowOnServer = Carbon::now()->format('Y-m-d H:i:s'); // UTC
        $upcomingLessonsLocations = $this
            ->select(DB::raw('DATE(start) as date_day_start'), 'city', 'state')
            ->groupBy('date_day_start', 'city', 'state')
            ->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) <= lessons.start")
            ->whereRaw(" ( lessons.is_cancelled is NULL OR lessons.is_cancelled=0 ) ")
            ->where('instructor_id', $instructor_id)
            ->where('lessons.lesson_type', 'in_person')
            ->orderBy('date_day_start', 'asc')
            ->get();

        $prevLessonLocation = null;
        $return = [];
        foreach($upcomingLessonsLocations as $ll) {
            $currentLessonLocation = [
                'date_day_start' => $ll->date_day_start,
                'date_day_end' => $ll->date_day_start,
                'city' => $ll->city,
                'state' => $ll->state,
            ];
            if(
                $prevLessonLocation == null
                ||
                ($prevLessonLocation['city'] != $currentLessonLocation['city'] && $prevLessonLocation['state'] != $currentLessonLocation['state'])
            ) {
                $return[] = $currentLessonLocation;
            } else {
                $return[count($return) - 1]['date_day_end'] = $currentLessonLocation['date_day_start'];
            }

            $prevLessonLocation = $currentLessonLocation;
        }

        return $return;
    }

    /**
     * @param $instructor_id
     * @return array
     * @throws RepositoryException
     */
    public function getUpcomingInstructorVirtualLessonsDates($instructor_id = null)
    {
        $this->resetCriteria();
        $this->resetScope();
        if(!$instructor_id)
            $instructor_id = Auth::user()->id;
        $this->pushCriteria(new LessonInFutureCriteria());
        $nowOnServer = Carbon::now()->format('Y-m-d H:i:s');
        $upcomingLessonsLocations = $this->select(DB::raw('DATE(start) as date_day_start'), 'genre_id')
            ->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) <= lessons.start")
            ->whereRaw(" ( lessons.is_cancelled is NULL OR lessons.is_cancelled=0 ) ")
            ->where('lessons.lesson_type', 'virtual')
            ->where('instructor_id', $instructor_id)
            ->groupBy('date_day_start', 'genre_id')
            ->orderBy('date_day_start', 'asc')->with('genre')
            ->get();


        $prevLessonLocation = null;
        $return = [];
        foreach($upcomingLessonsLocations as $ll) {
            $currentLessonLocation = [
                'date_day_start' => $ll->date_day_start,
                'date_day_end' => $ll->date_day_start,
                'genre_title' => $ll->genre->title,
                'genre_id' => $ll->genre_id,
            ];
            if(
                $prevLessonLocation == null
                ||
                ($prevLessonLocation['genre_id'] != $currentLessonLocation['genre_id'])
            ) {
                $return[] = $currentLessonLocation;
            } else {
                $return[count($return) - 1]['date_day_end'] = $currentLessonLocation['date_day_start'];
            }

            $prevLessonLocation = $currentLessonLocation;
        }

        return $return;
    }


    /**
     * @param $instructor_id
     * @return LengthAwarePaginator|Collection|mixed
     * @throws RepositoryException
     */
    public function getInstructorUpcomingLessons($instructor_id)
    {
        $this->resetCriteria();
        $this->resetScope();

        if(!$instructor_id)
            $instructor_id = Auth::user()->id;

        $this->pushCriteria(new LessonInFutureCriteria());
        if(!Auth::user() || $instructor_id != Auth::user()->id)
            $this->pushCriteria(new LessonOnlyPublic());

        return $this->scopeQuery(function($query) use ($instructor_id) {
            return $query->where('instructor_id', $instructor_id);
        })->get();
    }

    /**
     * @param $ip
     * @return array
     * @throws RepositoryException
     */
    public function upcomingNearbyLessons($ip)
    {
        $this->resetCriteria();
        $this->resetScope();
        $userGeoCoordinates = [];
        $preferredGenresIds = [];

        if(Auth::user() && Auth::user()->hasRole(User::ROLE_STUDENT)) {
            $preferredGenresIds = Auth::user()->genres()->pluck('id')->all();
        }
        $this->pushCriteria(new LessonOnlyPublic());
        if(Auth::user() && count($userGeoLocations = Auth::user()->geoLocations()->get()) > 0) {
            foreach($userGeoLocations as $geoLocation) {
                if(
                    $geoLocation->lat != ''
                    && $geoLocation->lng != ''
                    && $geoLocation->limit != ''
                    && strtotime(($geoLocation->date_from . ' 00:00:00')) <= strtotime(Carbon::now($geoLocation->timezone_id)->format('Y-m-d H:i:s'))
                    && strtotime(($geoLocation->date_to . ' 23:59:59')) >= strtotime(Carbon::now($geoLocation->timezone_id)->format('Y-m-d H:i:s'))
                )
                    $userGeoCoordinates[] = [
                        'lat' => $geoLocation->lat,
                        'lng' => $geoLocation->lng,
                        'limit' => $geoLocation->limit,
                        'date_from' => $geoLocation->date_from . ' 00:00:00',
                        'date_to' => $geoLocation->date_to . ' 23:59:59',
                    ];
            }
        } else {
            if($ip == '127.0.0.1')
                $ip = '208.118.229.134';

            $geoLocation = geoip($ip);
            if($geoLocation instanceof Location && $geoLocation->getAttribute('country') == 'United States') {
                $userGeoCoordinates[] = [
                    'lat' => $geoLocation->getAttribute('lat'),
                    'lng' => $geoLocation->getAttribute('lon'),
                    'limit' => UserGeoLocation::getDefaultLimit(),
                    'date_from' => now()->format('Y-m-d H:i:s'),
                    'date_to' => now()->addDay()->format('Y-m-d H:i:s')
                ];
            }
        }

        $mathingLessonsIds = [];
        foreach($userGeoCoordinates as $index => $userGeoCoordinate) {
            $nowOnServer = Carbon::now()->format('Y-m-d H:i:s'); // UTC
            $geoMatchingLessons = $this->model->select('lessons.id', 'lessons.spots_count')
                ->leftJoin('bookings', function($join) {
                    $join->on('lessons.id', '=', 'bookings.lesson_id')
                        ->whereRaw(" ( bookings.status <> 'cancelled' OR bookings.status IS NULL ) ");
                })
                ->whereRaw(" ( lessons.is_cancelled is NULL OR lessons.is_cancelled=0 ) ")
                ->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) <= lessons.start")
                ->havingRaw("COUNT(lessons.id) < spots_count")
                ->groupBy('lessons.id');

            if(config('app.env') == 'prod') {
                $geoMatchingLessons->join('users', 'lessons.instructor_id', '=', "users.id")
                    ->where('users.status', User::STATUS_ACTIVE)
                    ->whereNotNull('users.bt_submerchant_id')
                    ->where('users.bt_submerchant_status', MerchantAccount::STATUS_ACTIVE);
            }

            // only interested genres for students
            if(count($preferredGenresIds))
                $geoMatchingLessons->whereIn('lessons.genre_id', $preferredGenresIds);
            $geoMatchingLessons = $geoMatchingLessons->get()
                ->pluck('id')
                ->all();

            $mathingLessonsIds = array_merge(
                $mathingLessonsIds,
                $geoMatchingLessons
            );
        }

        $mathingLessonsIds = array_unique($mathingLessonsIds);
        if(count($mathingLessonsIds)) {
            $upcomingNearByLessons = Lesson::whereIn('id', $mathingLessonsIds)
                ->limit(10)
                ->orderBy('start', 'asc')
                ->with(['genre', 'instructor', 'instructor.profile', 'students'])
                ->get();

            return $upcomingNearByLessons;
        }
        return [];
    }


    /**
     * @param Lesson $lesson
     * @param $distance
     * @return array|LengthAwarePaginator|Collection|mixed
     */
    public function sameDayUpcomingNearbyLessonLocationLessons(Lesson $lesson, $distance = null)
    {
        $this->resetCriteria();
        $this->resetScope();

        if($distance == null)
            $distance = UserGeoLocation::getDefaultLimit();
        else
            $distance = (int)$distance;

        $lessonGeoCoordinate = [
            'lat' => $lesson->lat,
            'lng' => $lesson->lng,
            'limit' => $distance,
        ];
        $nowOnServer = Carbon::now()->format('Y-m-d H:i:s'); // UTC
        $mathingLessonsIds = $this->model->select('lessons.id', 'lessons.spots_count')
            ->addSelect(DB::raw("get_distance_in_miles_between_geo_locations({$lessonGeoCoordinate['lat']},{$lessonGeoCoordinate['lng']}, lat, lng) as distance"))
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->leftJoin('bookings', function($join) {
                $join->on('lessons.id', '=', 'bookings.lesson_id')
                    ->whereRaw(" ( bookings.status <> 'cancelled' OR bookings.status IS NULL ) ");
            })
            ->whereRaw(" ( lessons.is_cancelled is NULL OR lessons.is_cancelled=0 ) ")
            ->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) <= lessons.start")
            ->whereRaw("DATE(lessons.start) = '{$lesson->start->format('Y-m-d')}'")
            ->where('lessons.genre_id', $lesson->genre_id)
            ->where('lessons.id', '<>', $lesson->id)
            ->having("distance", '<', $lessonGeoCoordinate['limit'])
            ->havingRaw("COUNT(lessons.id) < spots_count")
            ->groupBy('lessons.id');

        if(config('app.env') == 'prod') {
            $mathingLessonsIds->join('users', 'lessons.instructor_id', '=', "users.id")
                ->where('users.status', User::STATUS_ACTIVE)
                ->whereNotNull('users.bt_submerchant_id')
                ->where('users.bt_submerchant_status', MerchantAccount::STATUS_ACTIVE);
        }

        $mathingLessonsIds = $mathingLessonsIds->get()
            ->pluck('id')
            ->all();

        $mathingLessonsIds = array_unique($mathingLessonsIds);

        if(count($mathingLessonsIds)) {
            $upcomingNearByLessons = $this->scopeQuery(function($query) use ($mathingLessonsIds) {
                $query = $query->whereIn('id', $mathingLessonsIds)
                    ->orderBy('start', 'asc');
                return $query;
            })->with(['genre', 'instructor', 'instructor.profile'])
                ->get();

            return $upcomingNearByLessons;
        }

        return [];
    }

    // approved and payed

    /**
     * @param $instructorId
     * @param $period
     * @return array
     */
    public function getCountBookedLessonsForPeriod($instructorId, $period = '')
    {
        $this->resetCriteria();
        $this->resetScope();
        $this->scopeQuery(function($query) use ($instructorId, $period) {
            $nowOnServer = Carbon::now()->format('Y-m-d H:i:s'); // UTC
            $query = $query->select(DB::raw('COUNT( DISTINCT lessons.id) as countBookedInPeriod'));
            if($period == '') {
                $query = $query->addSelect(DB::raw("YEAR(lessons.start) as lessonsPeriod"));
            } else {
                $query = $query->addSelect(DB::raw("MONTH(lessons.start) as lessonsPeriod"))
                    ->whereRaw("YEAR(lessons.start) = $period");
            }
            $query = $query->groupBy(DB::raw('lessonsPeriod'))
                ->join('bookings', 'lessons.id', '=', "bookings.lesson_id")
                ->whereRaw(" ( lessons.is_cancelled is NULL OR lessons.is_cancelled=0 ) ")
                ->whereIn('bookings.status', [Booking::STATUS_COMPLETE, Booking::STATUS_ESCROW_RELEASED, Booking::STATUS_ESCROW, Booking::STATUS_UNABLE_ESCROW_RELEASE])
                ->where('lessons.instructor_id', $instructorId);
            return $query;
        });
        $count = [];
        $this->get(['countBookedInPeriod', 'lessonsPeriod'])->each(function($item) use (&$count) {
            $count[$item->lessonsPeriod] = $item->countBookedInPeriod;
        });
        return $count;
    }

    // which were booked also

    /**
     * @param $instructorId
     * @param $period
     * @return array
     */
    public function getCountCompleteLessonsForPeriod($instructorId, $period = '')
    {
        $this->resetCriteria();
        $this->resetScope();

        $this->scopeQuery(function($query) use ($instructorId, $period) {
            $nowOnServer = Carbon::now()->format('Y-m-d H:i:s'); // UTC
            $query = $query->select(DB::raw('COUNT( DISTINCT lessons.id ) as countLessonsInPeriod'));
            if($period == '') {
                $query = $query->addSelect(DB::raw("YEAR(lessons.start) as lessonsPeriod"));
            } else {
                $query = $query->addSelect(DB::raw("MONTH(lessons.start) as lessonsPeriod"))
                    ->whereRaw("YEAR(lessons.start) = $period");
            }
            $query = $query->groupBy(DB::raw('lessonsPeriod'))
                ->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) >= lessons.start")  // past lessons
                ->whereRaw(" ( lessons.is_cancelled is NULL OR lessons.is_cancelled=0 ) ")
                ->where('lessons.instructor_id', $instructorId)
                ->join('bookings', function($join) {
                    $join->on('lessons.id', '=', 'bookings.lesson_id')
                        ->whereRaw(" ( bookings.status IN ('" . Booking::STATUS_COMPLETE . "','" . Booking::STATUS_ESCROW_RELEASED . "','" . Booking::STATUS_ESCROW . "','" . Booking::STATUS_UNABLE_ESCROW_RELEASE . "') ) ");
                });;

            return $query;
        });
        $count = [];
        $this->get(['countLessonsInPeriod', 'lessonsPeriod'])->each(function($item) use (&$count) {
            $count[$item->lessonsPeriod] = $item->countLessonsInPeriod;
        });
        return $count;
    }

    /**
     * @return int
     * @throws RepositoryException
     */
    public function countInstructorsOfFutureLessons()
    {
        $this->resetCriteria();
        $this->resetScope();
        $this->pushCriteria(new LessonOfActiveInstructorsCriteria());
        $this->pushCriteria(new LessonInFutureCriteria());
        $this->scopeQuery(function($query) {
            $query = $query->select('lessons.*')
                ->join('users', 'lessons.instructor_id', '=', "users.id")
                ->whereRaw(" ( lessons.is_cancelled is NULL OR lessons.is_cancelled=0 ) ")
                ->groupBy('lessons.instructor_id');
            return $query;
        });

        return $this->count();
    }

    /**
     * @return int
     * @throws RepositoryException
     */
    public function countActiveInstructors()
    {
        $this->resetCriteria();
        $this->resetScope();
        $this->pushCriteria(new LessonOfActiveInstructorsCriteria());
        $this->scopeQuery(function($query) {
            $nowOnServer = Carbon::now()->format('Y-m-d H:i:s'); // UTC
            $fromDate = Carbon::now()->subDays(30)->format('Y-m-d H:i:s'); // UTC
            $query = $query->select('lessons.*')
                ->join('users', 'lessons.instructor_id', '=', "users.id")
                ->whereRaw("( ( lessons.is_cancelled is NULL OR lessons.is_cancelled=0 ) AND ( lessons.start BETWEEN CONVERT_TZ('$fromDate', 'GMT', lessons.timezone_id) AND CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) ) )
						   OR
						   (lessons.created_at BETWEEN '$fromDate' AND '$nowOnServer')")
                ->groupBy('lessons.instructor_id');
            return $query;
        });

        return $this->count();
    }

    /**
     * @return LengthAwarePaginator|Collection|mixed
     * @throws RepositoryException
     */
    public function getAverageLessonsPerGenre()
    {
        $this->resetCriteria();
        $this->resetScope();
        $this->pushCriteria(new LessonOfActiveInstructorsCriteria());
        $this->pushCriteria(new LessonInFutureCriteria());
        $this->scopeQuery(function($query) {
            $query = $query->select('lessons.*', DB::raw('COUNT(lessons.genre_id) as count_lessons'), DB::raw('genres.title as genre_title'))
                ->join('users', 'lessons.instructor_id', '=', "users.id")
                ->join('genres', 'lessons.genre_id', '=', "genres.id")
                ->whereRaw(" ( lessons.is_cancelled is NULL OR lessons.is_cancelled=0 ) ")
                ->orderBy('genres.title')
                ->groupBy('lessons.genre_id');
            return $query;
        });

        return $this->all();
    }

    /**
     * @return int
     * @throws RepositoryException
     */
    public function countFutureLessons()
    {
        $this->resetCriteria();
        $this->resetScope();
        $this->pushCriteria(new LessonOfActiveInstructorsCriteria());
        $this->pushCriteria(new LessonInFutureCriteria());
        $this->scopeQuery(function($query) {
            $query = $query->select('lessons.*')
                ->join('users', 'lessons.instructor_id', '=', "users.id")
                ->whereRaw(" ( lessons.is_cancelled is NULL OR lessons.is_cancelled=0 ) ");
            return $query;
        });

        return $this->count();
    }

    /**
     * @param $limit
     * @return LengthAwarePaginator|Collection|mixed
     */
    public function getEndedLessons($limit = null)
    {
        $this->resetCriteria();
        $this->resetScope();
        $nowOnServer = Carbon::now()->format('Y-m-d H:i:s'); // UTC
        $this->model = $this->model
            ->select('lessons.*')
            ->where('lesson_type', 'virtual')
            ->whereRaw("lessons.end <= CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id)")
            ->whereRaw(" ( lessons.is_cancelled is NULL OR lessons.is_cancelled=0 ) ")
            ->whereNotNull('room_sid')
            ->whereNull('room_completed')
            ->orderBy('lessons.end', 'asc');
        if($limit)
            $this->model = $this->model->limit($limit);

        return $this->get();
    }

    /**
     * @param $limit
     * @return mixed
     */
    public function getTooLongTimeNotBookedPrivateLessons($limit = null)
    {
        $this->resetCriteria();
        $this->resetScope();
        $this->scopeQuery(function($query) use ($limit) {
            $timeToApprove = Setting::getValue('time_to_book_lesson_request', 24);
            $nowOnServer = Carbon::now()->format('Y-m-d H:i:s'); // UTC

            // apply all criterias first
            $query = $query->select('lessons.*', DB::raw("SUM(case when ( bookings.id IS NULL ) then 0 else 1 end) as count_booked"))
                ->whereNotNull('private_for_student_id')
                ->leftJoin('bookings', function($join) {
                    $join->on('lessons.id', '=', 'bookings.lesson_id')
                        ->whereRaw(" ( bookings.status <> 'cancelled' OR bookings.status IS NULL ) ");
                })
                ->whereRaw(" ( lessons.is_cancelled is NULL OR lessons.is_cancelled=0 ) ")
                ->whereRaw("( ( created_at <= DATE_SUB('$nowOnServer', INTERVAL $timeToApprove HOUR) ) OR lessons.start <= CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) )")
                ->groupBy('lessons.id');

            // use $query as subquery
            $filterQuery = Lesson::selectRaw('lessons.*')->fromSub($query, 'lessons')
                ->where('lessons.count_booked', 0);

            if($limit)
                $filterQuery->limit($limit);

            return $filterQuery;
        });

        return $this->model->get();
    }
}
