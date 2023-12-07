<?php

namespace App\Http\Controllers;

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
     * @param LessonRepository $lessonRepo
     * @param GenreRepository $genreRepository
     * @param UserRepository $userRepository
     * @return Application|Factory|View
     * @throws RepositoryException
     */
    public function index( LessonRepository $lessonRepo, GenreRepository $genreRepository, UserRepository $userRepository): View|Factory|Application
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

    	$galleryLimit = 4;
        $vars = [
            'page_title'	=> 'Dashboard',
			'userMedia'		=> Auth::user()->getGalleryMedia($galleryLimit),
			'siteGenres'	=> $genreRepository->presentResponse($genreRepository->getSiteGenres())['data'],
			'userGenres'	=> $genreRepository->presentResponse(Auth::user()->genres)['data'],
			'clients'		=> $clients,
			'bookings'		=> [], //  disabled
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

        $vars['showReminder'] = false;
        if (Auth::user()->bt_submerchant_id && !Auth::user()->pp_merchant_id ) {
            $vars['showReminder'] = true;
        }

        return view('frontend.instructor.dashboard', $vars);
    }

}
