<?php

namespace App\Http\Controllers;

use App\Repositories\GenreRepository;
use App\Repositories\PreRLessonRepository;
use App\Http\Requests\PreRLessonsFilterRequest;

class GlobalShopController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PreRLessonsFilterRequest $request, GenreRepository $genreRepository, PreRLessonRepository $preRLessonRepository)
    {

        $lessons = $preRLessonRepository->getPreRLessons($request);

        $preRLessonRepository->setPresenter("App\\Presenters\\PreRLessonInListPresenter");
        $lessons = $preRLessonRepository->presentResponse($lessons);

        $vars = [
            'page_title'    => 'Shop for Lessons and Tutorials',
            'siteGenres' => $genreRepository->presentResponse($genreRepository->getSiteGenres())['data'],
            'lessons' => $lessons
        ];

        return view('frontend.globalshop', $vars);
    }
}
