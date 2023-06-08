<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Repositories\PurchasedLessonRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class StudentLibraryAPIController extends AppBaseController
{
    private $purshLessonRepo;

    public function __construct(PurchasedLessonRepository $purshLessonRepo)
    {
        $this->purshLessonRepo = $purshLessonRepo;
        parent::__construct();
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $studentLessons = $this->purshLessonRepo->getStudentPurchasedLessons($request, Auth::user()->id);
        try{
			$this->purshLessonRepo->setPresenter("App\\Presenters\\PurchasedLessonInListPresenter");
			$studentLessons = $this->purshLessonRepo->presentResponse($studentLessons);
            Log::info($studentLessons);
		}catch (\Exception $e){
			Log::error('getStudentBookings : ' . $e->getMessage());
			$studentLessons = ['data'=>[]];
		}

        return $this->sendResponse($studentLessons);
    }

    /**
     * @param Request $request
     * @param $lesson
     * @return JsonResponse
     */
    public function getStudentLessonById(Request $request, $lesson)
    {
		$lesson = $this->purshLessonRepo->findWithoutFail((int)$lesson);

		if (empty($lesson)) {
			return $this->sendError('Lesson not found');
		}

        $this->purshLessonRepo->setPresenter("App\\Presenters\\PurchasedLessonSinglePresenter");
		$lesson = $this->purshLessonRepo->presentResponse( $lesson );
		return $this->sendResponse($lesson);
    }

    /**
     * @return JsonResponse
     */
    public function getStudentGenres()
    {
        $genres = Auth::user()->genres;
        if (empty($genres)) {
            return $this->sendError('Genres not found');
        }
        return $this->sendResponse($genres);

    }

}
