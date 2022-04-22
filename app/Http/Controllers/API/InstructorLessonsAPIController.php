<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLessonAPIRequest;
use App\Http\Requests\API\UpdateLessonAPIRequest;
use App\Models\Lesson;
use App\Models\Booking;
use App\Models\User;
use App\Models\LessonRequest;
use App\Repositories\LessonRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;
use App\Exports\InstructorLessonsExport;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use Log;
use Illuminate\Support\Carbon;

/**
 * Class LessonController
 * @package App\Http\Controllers\API
 */

class InstructorLessonsAPIController extends AppBaseController
{
    /** @var  LessonRepository */
    private $lessonRepository;

    public function __construct(LessonRepository $lessonRepo)
    {
        $this->lessonRepository = $lessonRepo;
    }

    public function index(Request $request, User $instructor)
    {

        // ALLOW OPTIONS METHOD
        $headers = [
            'Access-Control-Allow-Methods'=> 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Headers'=> 'Content-Type, X-Auth-Token, Origin'
        ];
        if($request->getMethod() == "OPTIONS") {
            // The client-side application can set only headers allowed in Access-Control-Allow-Headers
            return Response::make('OK', 200, $headers);
        }

        if ($instructor == null) {
            $instructor = Auth::user();
        }
        $this->lessonRepository->setPresenter("App\\Presenters\\LessonInListPresenter");
        $lessons = $this->lessonRepository->presentResponse($this->lessonRepository->getInstructorLessons($request, $instructor->id))['data'];

        return $this->sendResponse($lessons);
    }

    public function getLessonById(Request $request, $lesson)
    {

        // ALLOW OPTIONS METHOD
        $headers = [
            'Access-Control-Allow-Methods'=> 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Headers'=> 'Content-Type, X-Auth-Token, Origin'
        ];
        if($request->getMethod() == "OPTIONS") {
            // The client-side application can set only headers allowed in Access-Control-Allow-Headers
            return Response::make('OK', 200, $headers);
        }

        $lessons = Lesson::find($lesson);

        $this->lessonRepository->setPresenter("App\\Presenters\\LessonInListPresenter");

        return $this->sendResponse($lessons);
    }

    public function export(Request $request)
    {
        $this->lessonRepository->setPresenter("App\\Presenters\\LessonInListPresenter");
        $local_time = strtotime($request->get('local_time', 'now'));
        return Excel::download(new \App\Exports\InstructorLessonsExport($this->lessonRepository, $request, Auth::user()->id, $local_time), 'lessons-list.xlsx');
    }

    public function dashboardLessons(Request $request)
    {

        $this->lessonRepository->setPresenter("App\\Presenters\\LessonDashboardPresenter");

        try {
            $lessons = $this->lessonRepository->presentResponse($this->lessonRepository->getDashboardInstructorLessons($request, Auth::user()->id));
        } catch (\Exception $e) {
            \Log::error('getDashboardInstructorLessons : ' . $e->getMessage());
            $lessons = ['data' => []];
        }

        return $this->sendResponse($lessons);
    }

    public function pendingLessonsCount()
    {

        $nowOnServer = Carbon::now()->format('Y-m-d H:i:s');

        $pendingLessons = \DB::table('bookings')
            ->where('lessons.instructor_id', '=', Auth::id())
            ->join('lessons', 'bookings.lesson_id', '=', 'lessons.id')
            ->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) < lessons.start")
            ->where('status', '=', 'pending')
            ->count();

        $pendingLessonRequests = \DB::table('lesson_requests')
            ->where("lesson_requests.instructor_id", Auth::id())
            ->where(function ($q) {
                $q->where("lesson_requests.status", LessonRequest::STATUS_APPROVED)
                    ->orWhere("lesson_requests.status", LessonRequest::STATUS_PENDING);
            })
            ->where('lesson_requests.lesson_id', '=', NULL)
            ->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lesson_requests.timezone_id) < lesson_requests.start")
            ->count();

        $response = array('pending' => $pendingLessons, 'requests' => $pendingLessonRequests);


        return $this->sendResponse($response);
    }

    private function createLessonsByInterval($input){
        $_start = $input['start'];
        $_end = $input['end'];

        $start = \DateTime::createFromFormat('Y-m-d H:i:s', "$_start")->getTimestamp() / 60;

        $end = \DateTime::createFromFormat('Y-m-d H:i:s', "$_end")->getTimestamp() / 60;

        $count = floor(($end - $start) / $input['time_interval']);

        for ($i = 0; $i <= $end - $start; $i += ($input['time_interval'] + $input['interval_break'])) {

            if (($i / $count) >= ($input['time_interval'] + $input['interval_break'])) {
                continue;
            }

            $iterLessonData = $input;

            $iterEnd = $iterLessonData['start'];
            $dummyEnd = new \DateTime();
            $iterEnd = (\DateTime::createFromFormat('Y-m-d H:i:s', "$iterEnd")->getTimestamp() / 60 + $i + $input['time_interval']) * 60;
            $dummyEnd = $dummyEnd->setTimestamp($iterEnd);
            $dummyEnd = $dummyEnd->format('Y-m-d H:i:s');
            $iterLessonData['end'] = $dummyEnd;


            $iterStart = $iterLessonData['start'];
            $dummyStart = new \DateTime();
            $iterStart = (\DateTime::createFromFormat('Y-m-d H:i:s', "$iterStart")->getTimestamp() / 60 + $i) * 60;
            $dummyStart = $dummyStart->setTimestamp($iterStart);
            $dummyStart = $dummyStart->format('Y-m-d H:i:s');
            $iterLessonData['start'] = $dummyStart;

            $lesson = $this->lessonRepository->create($iterLessonData);
        }

        return $lesson;
    }

    public function store(CreateLessonAPIRequest $request)
    {

        if (!Auth::user()->canAddNewLesson()) {
            return $this->sendError('To add new lesson you must connect a submerchant account to your profile, upload profile image and have at least one media item in gallery', 400);
        }
        $input = $this->_prepareInputData($request);

        if (!empty($input['recurrence_until']) && $input['recurrence_frequencies'] != "0") {

            $ty = $input['recurrence_frequencies'];

            $_start = $input['start'];
            $start = \DateTime::createFromFormat('Y-m-d H:i:s', "$_start")->getTimestamp();
            $__start = Carbon::createFromTimestamp($start);

            $_end = $input['end'];
            $end = \DateTime::createFromFormat('Y-m-d H:i:s', "$_end")->getTimestamp();
            $__end = Carbon::createFromTimestamp($end);


            $_until = $input['recurrence_until'];
            $until = \DateTime::createFromFormat('Y-m-d', "$_until")->getTimestamp();
            $__until = Carbon::createFromTimestamp($until);


            while ($__start->lt($__until)) {
                $iterLessonData = $input;

                $iterLessonData['start'] = $__start;
                $iterLessonData['end'] = $__end;

                if ($input['time_interval'] && $input['time_interval'] > 0) {
                    $lesson = $this->createLessonsByInterval($iterLessonData);
                }
                else{
                    $lesson = $this->lessonRepository->create($iterLessonData);
                }

                if ($ty === 'day') {
                    $__start->addDay();
                    $__end->addDay();
                } else if ($ty === 'week') {
                    $__start->addWeek();
                    $__end->addWeek();
                } else if ($ty === 'week2') {
                    $__start->addWeeks(2);
                    $__end->addWeeks(2);
                } else if ($ty === 'month') {
                    $__start->addMonth();
                    $__end->addMonth();
                }


            }

            return $this->sendResponse($this->lessonRepository->presentResponse($lesson)['data'], 'Lesson saved');
        }

        if (!empty($input['time_interval']) && $input['time_interval'] > 0) {
            $lesson = $this->createLessonsByInterval($input);

            return $this->sendResponse($this->lessonRepository->presentResponse($lesson)['data'], 'Lesson saved');
        }

        $lesson = $this->lessonRepository->create($input);

        return $this->sendResponse($this->lessonRepository->presentResponse($lesson)['data'], 'Lesson saved');
    }

    /**
     * Display the specified Lesson.
     * GET|HEAD /lessons/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Lesson $lesson */
        $lesson = $this->lessonRepository->findWithoutFail($id);

        if (empty($lesson)) {
            return $this->sendError('Lesson not found');
        }

        return $this->sendResponse($this->lessonRepository->presentResponse($lesson)['data'], 'Lesson retrieved');
    }

    /**
     * Update the specified Lesson in storage.
     * PUT/PATCH /lessons/{id}
     *
     * @param  int $id
     * @param UpdateLessonAPIRequest $request
     *
     * @return Response
     */
    public function update($lesson, UpdateLessonAPIRequest $request)
    {
        // when there are bookings enable updating price only

        // return $this->sendError('Updating lessons disabled');

        $input = $this->_prepareInputData($request);

        /** @var Lesson $lesson */
        $lesson = $this->lessonRepository->findWithoutFail($lesson);

        if (empty($lesson)) {
            return $this->sendError('Lesson not found');
        }

        if ($lesson['instructor_id'] != $request->user()->id)
            $this->sendError('Invalid Request', 403);


        if ($lesson->lesson_type == 'in_person') {
            $locationDetails = getLocationDetails($input['location']);

            $input['lat'] = isset($locationDetails['lat']) ? $locationDetails['lat'] : null;
            $input['lng'] = isset($locationDetails['lng']) ? $locationDetails['lng'] : null;
            $input['city'] = isset($locationDetails['city']) ? $locationDetails['city'] : null;
            $input['state'] = isset($locationDetails['state']) ? $locationDetails['state'] : null;
            $input['address'] = isset($locationDetails['address']) ? $locationDetails['address'] : null;
            $input['zip'] = isset($locationDetails['zip']) ? $locationDetails['zip'] : null;
            $input['timezone_id'] = isset($locationDetails['timezone_id']) ? $locationDetails['timezone_id'] : null;
        }


        $lesson = $this->lessonRepository->update($input, $lesson['id']);

        return $this->sendResponse($this->lessonRepository->presentResponse($lesson)['data'], 'Lesson updated');
    }
    /**
     * Remove the specified Lesson from storage.
     * DELETE /lessons/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    //    public function cancel($id)
    //    {
    //        /** @var Lesson $lesson */
    //        $lesson = $this->lessonRepository->findWithoutFail($id);
    //
    //        if (empty($lesson)) {
    //            return $this->sendError('Lesson not found');
    //        }
    //
    //		$cancelled = $lesson->cancel();
    //		if ($cancelled)
    //			return $this->sendResponse(true, 'Lesson cancelled successfully');
    //		else
    //			return $this->sendError('Can\'t cancel the lesson', 400);
    //    }

    private function _prepareInputData(Request $request)
    {
        // prepare data
        $input = $request->only([
            'genre',
            'date',
            'date_to',
            'time_from',
            'time_to',
            'spots_count',
            'spot_price',
            'location',
            'lesson_type',
            'timezone_id',
            'description',
            'time_interval',
            'recurrence_until',
            'recurrence_frequencies',
            'interval_break'
        ]);

        $input['start'] = $input['date'] . ' ' . $input['time_from'];
        unset($input['time_from']);

        $input['end'] = $input['date_to'] . ' ' . $input['time_to'];
        unset($input['time_to']);

        unset($input['date']);
        unset($input['date_to']);

        $input['instructor_id'] = $request->user()->id;

        $input['genre_id'] = $input['genre'];
        unset($input['genre']);

        return $input;
    }
}
