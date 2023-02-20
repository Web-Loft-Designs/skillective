<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\BookingRepository;
use App\Repositories\GenreRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Exceptions\RepositoryException;

class StudentDashboardController extends Controller
{

    /**
     * @param UserRepository $userRepository
     * @param BookingRepository $bookingRepository
     * @param GenreRepository $genreRepository
     * @return Application|Factory|View
     * @throws RepositoryException
     */
    public function index(UserRepository $userRepository, BookingRepository $bookingRepository, GenreRepository $genreRepository)
    {
        try{
			$request = new Request([
				'limit'	=> 5
			]);
			$userRepository->setPresenter("App\\Presenters\\StudentInstructorInListPresenter");
            $instructors = $userRepository->presentResponse($userRepository->getStudentInstructors(Auth::user()->id, $request));
        }catch (\Exception $e){
            Log::error('getStudentInstructors : ' . $e->getMessage());
            $instructors = ['data'=>[]];
        }

		try{
			$request = new Request([
				'limit'	=> 5,
				'type'	=> 'current'
			]);
			$bookingRepository->setPresenter("App\\Presenters\\BookingInListPresenter");
			$bookings = $bookingRepository->presentResponse($bookingRepository->getStudentBookings($request, Auth::user()->id));
		}catch (\Exception $e){
			Log::error('getStudentBookings : ' . $e->getMessage());
			$bookings = ['data'=>[]];
		}

        // Genre Learning block
		$bookedGenresStatistics = $bookingRepository->getStudentBookedGenresStatistics(Auth::user()->id);
    	$limit = 4;
		$vars = [
			'page_title' => 'Dashboard',
			'userMedia'	=> Auth::user()->getGalleryMedia($limit),
            'instructors' => $instructors,
            'bookings' => $bookings,
			'studentGenres' => Auth::user()->genres,
			'bookedGenresStatistics' =>$bookedGenresStatistics,
			'siteGenres' => $genreRepository->getSiteGenres(),
		];

		$bookingRepository->setPresenter("App\\Presenters\\BookingSinglePresenter");
		$upcomingBooking = $bookingRepository->getStudentUpcomingBooking(Auth::user()->id);
		if ($upcomingBooking && Cookie::get('hiddenUpcomingBookingId')!=$upcomingBooking['id']){
			$vars['upcomingBooking'] = $bookingRepository->presentResponse($upcomingBooking)['data'];
		}

        return view('frontend.student.dashboard', $vars);
    }

}
