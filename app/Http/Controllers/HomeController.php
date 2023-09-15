<?php

namespace App\Http\Controllers;

use App\Repositories\LessonRepository;
use App\Repositories\PreRLessonRepository;
use App\Repositories\UserRepository;
use App\Repositories\GenreRepository;
use App\Repositories\TestimonialRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Exceptions\RepositoryException;


class HomeController extends AppBaseController
{
    /**
     * @param Request $request
     * @param GenreRepository $genreRepository
     * @param TestimonialRepository $testimonialRepository
     * @param LessonRepository $lessonRepository
     * @param UserRepository $userRepository
     * @param PreRLessonRepository $preRLessonRepository
     * @return Application|Factory|View
     * @throws RepositoryException
     */
    public function index(Request $request,GenreRepository $genreRepository, TestimonialRepository $testimonialRepository, LessonRepository $lessonRepository, UserRepository $userRepository, PreRLessonRepository $preRLessonRepository): View|Factory|Application
    {
		$userIpLocation = '';
		if (!$this->currentPage) {
			$this->currentPage = getCurrentPage('/');
			view()->share( 'currentPage', $this->currentPage );
		}
        $request->query->set('lesson_type', 'pre_recorded');
        $lessons = $preRLessonRepository->getTrendingPreRLessons($request);
        $preRLessonRepository->setPresenter("App\\Presenters\\PreRLessonInListPresenter");
        $lessons = $preRLessonRepository->presentResponse($lessons);

    	$vars = [
			'siteGenres' => $genreRepository->presentResponse($genreRepository->getSiteGenres())['data'],
            'siteInstructorsGenres' => $genreRepository->presentResponse($genreRepository->getSiteInstructorsGenres())['data'],
			'featuredGenres' => $genreRepository->presentResponse($genreRepository->getFeatured(9))['data'],
			'featuresInstructors' => $userRepository->presentResponse($userRepository->getInstructorsForHome())['data'],
			'testimonials' => $testimonialRepository->orderBy('position', 'asc')->all(),
			'upcomingNearbyLessons' => $lessonRepository->upcomingNearbyLessons(request()->ip()),
			'userIpLocation' => $userIpLocation,
            'userGenres' => Auth::check() ? $userRepository->presentResponse(Auth::user()->genres)['data'] : [],
            'lessons' => $lessons
		];
        return view('home', $vars);
    }
}
