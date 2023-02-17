<?php

namespace App\Http\Controllers\API;

use App\Models\Booking;
use App\Models\Lesson;
use App\Http\Requests\API\CancelLessonsAPIRequest;
use App\Repositories\LessonRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Response;
use Prettus\Repository\Exceptions\RepositoryException;


class LessonsAPIController extends AppBaseController
{
    /** @var  LessonRepository */
    private $lessonRepository;

    public function __construct(LessonRepository $lessonRepo)
    {
        parent::__construct();
        $this->lessonRepository = $lessonRepo;
    }


    /**
     * @param Request $request
     * @param $lesson
     * @return Application|ResponseFactory|JsonResponse|Response
     */
    public function details(Request $request, $lesson)
	{
        // ALLOW OPTIONS METHOD
        $headers = [
            'Access-Control-Allow-Methods'=> 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Headers'=> 'Content-Type, X-Auth-Token, Origin'
        ];
        if($request->getMethod() == "OPTIONS") {
            // The client-side application can set only headers allowed in Access-Control-Allow-Headers
            return response('OK', 200, $headers);

        }
		$lesson = $this->lessonRepository->findWithoutFail((int)$lesson);
		if (empty($lesson)) {
			return $this->sendError('Lesson not found');
		}
		$this->lessonRepository->setPresenter("App\\Presenters\\LessonSinglePresenter");
		$lesson = $this->lessonRepository->presentResponse( $lesson );
		return $this->sendResponse($lesson);
	}


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws RepositoryException
     */
    public function upcomingLessons(Request $request)
	{
		$lessons = $this->lessonRepository->upcomingNearbyLessons($request->ip());
		return $this->sendResponse($lessons);
	}

    /**
     * @param $id
     * @return JsonResponse
     */
    public function cancel($id)
	{
		/** @var Lesson $lesson */
		$lesson = $this->lessonRepository->findWithoutFail($id);

		if (empty($lesson)) {
			return $this->sendError('Lesson not found');
		}
		if ($lesson->bookings()->whereIn('bookings.status', [Booking::STATUS_PENDING, Booking::STATUS_APPROVED, Booking::STATUS_ESCROW])->count()>0) {
			return $this->sendError('Can\'t cancel lesson. You have to do something with this lesson\'s bookings first.');
		}
		if ($lesson->is_cancelled!=1 && !$lesson->alreadyStarted() ) {
			$lesson->cancel();
			return $this->sendResponse( true, 'Lesson cancelled' );
		}
		return $this->sendError( 'Can\'t cancel the lesson', 400 );
	}

    /**
     * @param CancelLessonsAPIRequest $request
     * @return JsonResponse
     */
    public function cancelMany(CancelLessonsAPIRequest $request)
	{
		$count_cancelled = 0;
		$count_not_cancelled = 0;
		foreach ($request->input('lessons') as $lessonId){
			$lesson = $this->lessonRepository->findWithoutFail($lessonId);
			if (!$lesson->bookings()->whereIn('bookings.status', [Booking::STATUS_PENDING, Booking::STATUS_APPROVED, Booking::STATUS_ESCROW])->count()>0) {
				if ($lesson->is_cancelled!=1 && !$lesson->alreadyStarted() ) {
					$lesson->cancel();
					$count_cancelled++;
				}else
					$count_not_cancelled++;
			}else{
				$count_not_cancelled++;
			}
		}

		return $this->sendResponse(true, $count_cancelled . ' lessons cancelled');
	}
}
