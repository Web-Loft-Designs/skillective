<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;
use App\Repositories\LessonRepository;
use App\Repositories\GenreRepository;

class InstructorScheduleController extends Controller
{
	/** @var  LessonRepository */
	private $lessonRepository;

	/** @var  GenreRepository */
	private $genreRepository;
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, LessonRepository $lessonRepository, GenreRepository $genreRepository)
    {
        $vars = [
            'page_title'	=> 'Schedule',
			'siteGenres'	=> $genreRepository->presentResponse($genreRepository->getSiteGenres())['data'],
			'userGenres'	=> $genreRepository->presentResponse(Auth::user()->genres)['data'],
        ];

        return view('frontend.instructor.schedule', $vars);
    }

}
