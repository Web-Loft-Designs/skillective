<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use App\Repositories\GenreRepository;
use Illuminate\Support\Facades\Auth;

class InstructorScheduleController extends Controller
{

    /**
     * @param GenreRepository $genreRepository
     * @return Application|Factory|View
     */
    public function index(GenreRepository $genreRepository)
    {
        $vars = [
            'page_title'	=> 'Schedule',
			'siteGenres'	=> $genreRepository->presentResponse($genreRepository->getSiteGenres())['data'],
			'userGenres'	=> $genreRepository->presentResponse(Auth::user()->genres)['data'],
        ];

        return view('frontend.instructor.schedule', $vars);
    }

}
