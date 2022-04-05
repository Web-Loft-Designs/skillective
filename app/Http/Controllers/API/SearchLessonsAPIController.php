<?php

namespace App\Http\Controllers\API;


use App\Models\Lesson;
use App\Repositories\LessonRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Log;

/**
 * Class LessonController
 * @package App\Http\Controllers\API
 */

class SearchLessonsAPIController extends AppBaseController
{
    /** @var  LessonRepository */
    private $lessonRepository;

    public function __construct(LessonRepository $lessonRepo)
    {
        $this->lessonRepository = $lessonRepo;
    }

    /**
     * Display a listing of the Lesson.
     * GET|HEAD /lessons
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
    	try{
			$this->lessonRepository->setPresenter("App\\Presenters\\LessonInListPresenter");
			updateSearchReports($request);
			$lessons = $this->lessonRepository->presentResponse($this->lessonRepository->getFilteredAvailableLessons($request));
		}catch (\Exception $e){
    		Log::error('getFilteredAvailableLessons : ' . $e->getMessage());
    		$lessons = ['data'=>[]];
		}

        return $this->sendResponse($lessons);
    }

	public function getSameDayUpcomingNearbyLessonLocationLessons(Request $request, Lesson $lesson)
	{
		try{
			$distance = null;
			if ($request->filled('distance'))
				$distance = (int)$request->input('distance');

			$lessons =
//				$this->lessonRepository
//				->presentResponse(
					$this->lessonRepository->resetCriteria()->sameDayUpcomingNearbyLessonLocationLessons($lesson, $distance);
//	)['data'];
		}catch (\Exception $e){
			Log::error('getFilteredAvailableLessons : ' . $e->getMessage());
			$lessons = ['data'=>[]];
		}

		return $this->sendResponse($lessons);
	}
}
