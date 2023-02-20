<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\GenreRepository;
use App\Repositories\LessonRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InstructorBookingsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(GenreRepository $genreRepository, LessonRepository $lessonRepo)
    {
        try{
			$request = new Request([
				'limit'	=> 20,
				'type' => 'all',
				'sort' => 'date'
			]);
 			
			$lessonRepo->setPresenter("App\\Presenters\\LessonDashboardPresenter");
			
			$lessons = $lessonRepo->presentResponse($lessonRepo->getDashboardInstructorLessons($request, Auth::user()->id));

		}catch (\Exception $e){
			
			Log::error('getInstructorBookings : ' . $e->getMessage());
			$lessons = ['data'=>[]];
        }

		$vars = [
			'page_title' => 'Bookings',
			'bookings' => $lessons,
            'siteGenres'	=> $genreRepository->presentResponse($genreRepository->getSiteGenres())['data'],
            'userGenres'	=> $genreRepository->presentResponse(Auth::user()->genres)['data']
        ];

		return view('frontend.instructor.bookings', $vars);
    }
}
