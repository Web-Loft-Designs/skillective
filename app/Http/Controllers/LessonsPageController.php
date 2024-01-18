<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Http\Requests\LessonsFilterRequest;
use App\Repositories\GenreRepository;
use App\Repositories\LessonRepository;
use Illuminate\Support\Facades\Log;


class LessonsPageController extends Controller
{
    /**
     * @param LessonsFilterRequest $request
     * @param LessonRepository $lessonRepository
     * @param GenreRepository $genreRepository
     * @return Application|Factory|View
     */
    public function index(LessonsFilterRequest $request, LessonRepository $lessonRepository, GenreRepository $genreRepository)
    {
		try{
			$lessonRepository->setPresenter("App\\Presenters\\LessonInListPresenter");
			updateSearchReports($request);

			$lessons = $lessonRepository->presentResponse($lessonRepository->getFilteredAvailableLessons($request));
		}catch (\Exception $e){
			Log::error('getFilteredAvailableLessons : ' . $e->getMessage());
			$lessons = ['data'=>[]];
		}

    	$vars = [
    	    'userGenres' => 'userGenres',
    	    'siteGenres' => 'siteGenres',
			'genres' => $genreRepository->presentResponse($genreRepository->getSiteGenres())['data'],
			'lessons' => $lessons,
			'page_title' => 'Search Lessons'
		];
        return view('frontend.lessons', $vars);
    }

    public function locationDetails(Request $request){
		$location = $request->get('location', '');
		if ($location){
			$locationDetails = getLocationDetails($location);
			dd($locationDetails); // TODO шо за нах?

//			return (
//				array_search(null, $locationDetails)==false
//				&& in_array($locationDetails['location_type'], ['ROOFTOP', 'RANGE_INTERPOLATED'])
//			); // require exact location
		}
		print 'no data provided';
	}
}
