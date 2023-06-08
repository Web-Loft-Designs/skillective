<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Repositories\BookingRepository;
use App\Repositories\LessonRequestRepository;
use App\Repositories\GenreRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


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
     * @param BookingRepository $bookingRepository
     * @param GenreRepository $genreRepository
     * @param Request $request
     * @return Application|Factory|View
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
