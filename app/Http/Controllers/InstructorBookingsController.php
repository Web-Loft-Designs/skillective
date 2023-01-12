<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Repositories\BookingRepository;
use App\Repositories\GenreRepository;
use App\Repositories\LessonRequestRepository;
use App\Repositories\LessonRepository;
use Log;


class InstructorBookingsController extends Controller
{
    /** @var  LessonRequestRepository */
    private $lessonRequestRepository;

    public function __construct(LessonRequestRepository $lessonRequestRepository)
    {
        $this->lessonRequestRepository = $lessonRequestRepository;
        parent::__construct();
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(BookingRepository $bookingRepository, GenreRepository $genreRepository, LessonRepository $lessonRepo, Request $request)
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
