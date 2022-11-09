<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lesson;
use App\Repositories\BookingRepository;
use Illuminate\Http\Request;
use Auth;
use Cookie;
use App\Repositories\LessonRepository;
use App\Repositories\GenreRepository;
use App\Repositories\UserRepository;
use App\Models\Profile;
use Log;
use App\Notifications\YouMayBeInterestedInLessonNotification;
use TwilioVideo;

class InstructorDashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
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
		$uploadedAfter = null;
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
