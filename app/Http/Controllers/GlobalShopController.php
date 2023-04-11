<?php

namespace App\Http\Controllers;

use App\Repositories\GenreRepository;
use App\Repositories\PreRLessonRepository;
use App\Http\Requests\PreRLessonsFilterRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Prettus\Repository\Exceptions\RepositoryException;

class GlobalShopController extends Controller
{

    /**
     * @param PreRLessonsFilterRequest $request
     * @param GenreRepository $genreRepository
     * @param PreRLessonRepository $preRLessonRepository
     * @return Application|Factory|View
     * @throws RepositoryException
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
