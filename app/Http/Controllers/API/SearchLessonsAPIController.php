<?php

namespace App\Http\Controllers\API;


use App\Models\Lesson;
use App\Repositories\LessonRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Log;


class SearchLessonsAPIController extends AppBaseController
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
     * @return JsonResponse
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

    /**
     * @param Request $request
     * @param Lesson $lesson
     * @return JsonResponse
     */
    public function getSameDayUpcomingNearbyLessonLocationLessons(Request $request, Lesson $lesson)
	{
		try{
			$distance = null;
			if ($request->filled('distance'))
				$distance = (int)$request->input('distance');
			$lessons = $this->lessonRepository->resetCriteria()
                ->sameDayUpcomingNearbyLessonLocationLessons($lesson, $distance);

		}catch (\Exception $e){
			Log::error('getFilteredAvailableLessons : ' . $e->getMessage());
			$lessons = ['data'=>[]];
		}

		return $this->sendResponse($lessons);
	}
}
