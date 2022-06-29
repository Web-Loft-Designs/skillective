<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use App\Repositories\PurchasedLessonRepository;
use Auth;
use Log;

class StudentLibraryAPIController extends AppBaseController
{
    private $purshLessonRepo;

    public function __construct(PurchasedLessonRepository $purshLessonRepo)
    {
        $this->purshLessonRepo = $purshLessonRepo;
    }


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
     * @return \Illuminate\Http\JsonResponse
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
