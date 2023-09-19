<?php

namespace App\Http\Controllers;

use App\Repositories\GenreRepository;
use App\Repositories\PreRLessonRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Prettus\Repository\Exceptions\RepositoryException;

class GlobalShopController extends Controller
{

    /**
     * @param Request $request
     * @param GenreRepository $genreRepository
     * @param PreRLessonRepository $preRLessonRepository
     * @return Application|Factory|View
     * @throws RepositoryException
     */
    public function index(Request $request, GenreRepository $genreRepository, PreRLessonRepository $preRLessonRepository): View|Factory|Application
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
