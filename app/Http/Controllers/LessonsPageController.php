<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LessonsFilterRequest;
use App\Repositories\GenreRepository;
use App\Repositories\LessonRepository;
use Log;
use Cookie;

class LessonsPageController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(LessonsFilterRequest $request, LessonRepository $lessonRepository, GenreRepository $genreRepository)
    {
		try{
			$lessonRepository->setPresenter("App\\Presenters\\LessonInListPresenter");
//                	dd($lessonRepository->presentResponse($lessonRepository->getFilteredAvailableLessons($request)));
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
			dd($locationDetails);

//			return (
//				array_search(null, $locationDetails)==false
//				&& in_array($locationDetails['location_type'], ['ROOFTOP', 'RANGE_INTERPOLATED'])
//			); // require exact location
		}
		print 'no data provided';
	}
}
