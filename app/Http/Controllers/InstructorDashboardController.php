<?php

namespace App\Http\Controllers;

use App\Repositories\BookingRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Repositories\LessonRepository;
use App\Repositories\GenreRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Exceptions\RepositoryException;

class InstructorDashboardController extends Controller
{

    /**
     * @param Request $request
     * @param LessonRepository $lessonRepo
     * @param GenreRepository $genreRepository
     * @param UserRepository $userRepository
     * @param BookingRepository $bookingRepository
     * @return Application|Factory|View
     * @throws RepositoryException
     */
    public function index(Request $request, LessonRepository $lessonRepo, GenreRepository $genreRepository, UserRepository $userRepository, BookingRepository $bookingRepository)
    {
		try{
			$request = new Request([
				'limit'   => 5
			]);
			$clients = $userRepository->presentResponse($userRepository->getInstructorClients(Auth::user()->id, $request));
		}catch (\Exception $e){
			Log::error('getInstructorClients : ' . $e->getMessage());
			$clients = ['data'=>[]];
		}

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

    	$galleryLimit = 4;
        $vars = [
            'page_title'	=> 'Dashboard',
			'userMedia'		=> Auth::user()->getGalleryMedia($galleryLimit),
			'siteGenres'	=> $genreRepository->presentResponse($genreRepository->getSiteGenres())['data'],
			'userGenres'	=> $genreRepository->presentResponse(Auth::user()->genres)['data'],
			'clients'		=> $clients,
			'bookings'		=> $lessons,
            'tax_id'        => Auth::user()->tax_id
        ];

		$lessonRepo->setPresenter("App\\Presenters\\LessonSinglePresenter");
		$upcomingLesson = $lessonRepo->getInstructorUpcomingLesson();
		$upcomingLessons = $lessonRepo->getInstructorUpcomingLessons(null);

		if($upcomingLessons){
			$vars['upcomingLessons'] = $upcomingLessons;
		}

        if ($upcomingLesson && Cookie::get('hiddenUpcomingLessonId')!=$upcomingLesson['id']){
			$vars['upcomingLesson'] = $lessonRepo->presentResponse($upcomingLesson)['data'];
		}

        return view('frontend.instructor.dashboard', $vars);
    }

}
