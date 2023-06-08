<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateLessonRequestAPIRequest;
use App\Http\Requests\API\CancelLessonRequestAPIRequest;
use App\Http\Requests\API\AcceptLessonRequestAPIRequest;
use App\Models\Lesson;
use App\Models\LessonRequest;
use App\Repositories\LessonRepository;
use App\Repositories\LessonRequestRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Prettus\Validator\Exceptions\ValidatorException;


class LessonRequestAPIController extends AppBaseController
{
    /** @var  LessonRepository */
    private $lessonRepository;

    /** @var  LessonRequestRepository */
    private $lessonRequestRepository;

    public function __construct(LessonRepository $lessonRepo, LessonRequestRepository $lessonRequestRepository)
    {
        parent::__construct();
        $this->lessonRepository = $lessonRepo;
        $this->lessonRequestRepository = $lessonRequestRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try{
            $this->lessonRequestRepository->setPresenter("App\\Presenters\\LessonRequestInListPresenter");
            $requests = $this->lessonRequestRepository->presentResponse($this->lessonRequestRepository->getUserLessonRequests($request, Auth::user()->id));
        }catch (\Exception $e){
            Log::error('getUserLessonRequests: ' . $e->getMessage());
            $requests = ['data'=>[]];
        }

        return $this->sendResponse($requests);
    }

    /**
     * @param CreateLessonRequestAPIRequest $request
     * @return JsonResponse
     * @throws ValidatorException
     */
    public function store(CreateLessonRequestAPIRequest $request)
    {
        $input = $this->_prepareInputData($request);
        $input['status'] = LessonRequest::STATUS_PENDING;
        $lessonRequest = $this->lessonRequestRepository->create($input);
        $student = Auth::user();

        if ($lessonRequest->instructor->clients()->where('client_id', $student->id)->count() == 0){
            $lessonRequest->instructor->clients()->attach($student);
        }
        if ($student->instructors()->where('instructor_id', $lessonRequest->instructor_id)->count() == 0){
            $student->instructors()->attach($lessonRequest->instructor);
        }

        return $this->sendResponse(true, 'Lesson Request created');
    }

    /**
     * @param LessonRequest $lessonRequest
     * @param AcceptLessonRequestAPIRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function accept(LessonRequest $lessonRequest, AcceptLessonRequestAPIRequest $request)
    {
        if ($lessonRequest->status!=$lessonRequest::STATUS_PENDING)
            return $this->sendError('You are not able to cancel this request', 403);

		$input = $this->_prepareInputData($request);

		if ($lessonRequest->instructor_id != $request->user()->id)
			$this->sendError('Invalid Request', 403);

        $lessonRequest->instructor_note = $request->input('instructor_note');
        $lessonRequest->start = $input['start'];
        $lessonRequest->end = $input['end'];
        $lessonRequest->lesson_price = $input['lesson_price'];
        $lessonRequest->location = $request->input('location');
        $lessonRequest->save();

        $lessonRequest->saveQuietly($input);

        $lesson = new Lesson();
        $lesson = $lesson->createFromLessonRequest($lessonRequest);

        $lessonRequest->lesson_id = $lesson->id;
        $lessonRequest->status = LessonRequest::STATUS_APPROVED;
        $lessonRequest->save();

        return $this->sendResponse($this->lessonRequestRepository->presentResponse($lessonRequest)['data'], 'Lesson Request accepted');
    }


    /**
     * @param CancelLessonRequestAPIRequest $request
     * @param LessonRequest $lessonRequest
     * @return JsonResponse
     * @throws \Exception
     */
    public function cancel(CancelLessonRequestAPIRequest $request, LessonRequest $lessonRequest)
    {
        if (Auth::id()!=$lessonRequest->student_id && Auth::id()!=$lessonRequest->instructor_id){
			return $this->sendError('You are not able to cancel this request', 403);
		}
        if ($lessonRequest->status!=$lessonRequest::STATUS_PENDING)
            return $this->sendError('You are not able to cancel this request', 403);

		$cancelled = $lessonRequest->cancel($request->input('instructor_note'));
		if ($cancelled)
			return $this->sendResponse(true, 'Lesson Request cancelled');
		else
			return $this->sendError('Can\'t cancel the Lesson Request', 400);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function _prepareInputData(Request $request){
		// prepare data
		$input = $request->only([
			'genre',
            'date',
            'date_to',
			'time_from',
			'time_to',
			'count_participants',
			'lesson_price',
			'location',
            'lesson_type',
            'timezone_id',
            'instructor_id',
            'student_note',
            'instructor_note'
            ]);

		$input['start'] = $input['date'] . ' ' . $input['time_from'];
        unset($input['time_from']);


		$input['end'] = $input['date_to'] . ' ' . $input['time_to'];
		unset($input['time_to']);
        unset($input['date_to']);

		unset($input['date']);

		$input['student_id'] = $request->user()->id;

		$input['genre_id'] = $input['genre'];
		unset($input['genre']);

		return $input;
	}
}
