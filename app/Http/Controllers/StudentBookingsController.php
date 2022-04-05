<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;
use App\Repositories\BookingRepository;
use App\Repositories\LessonRequestRepository;
use App\Repositories\GenreRepository;
use Log;
use App\Models\Profile;

class StudentBookingsController extends Controller
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
    public function index(BookingRepository $bookingRepository, GenreRepository $genreRepository, Request $request)
    {
		try{
		    if ($request->get('type') == 'lesson_requests'){
                $this->lessonRequestRepository->setPresenter("App\\Presenters\\LessonRequestInListPresenter");
                $bookings = $this->lessonRequestRepository->presentResponse($this->lessonRequestRepository->getUserLessonRequests($request, Auth::user()->id));
            }else{
                $bookingRepository->setPresenter("App\\Presenters\\BookingInListPresenter");
                $bookings = $bookingRepository->presentResponse($bookingRepository->getStudentBookings($request, Auth::user()->id));
            }
		}catch (\Exception $e){
			Log::error('getStudentBookings : ' . $e->getMessage());
			$bookings = ['data'=>[]];
		}

		$vars = [
			'page_title' => 'Bookings',
            'siteGenres'	=> $genreRepository->presentResponse($genreRepository->getSiteGenres())['data'],
			'bookings' => $bookings,
		];

		return view('frontend.student.bookings', $vars);
    }

}
