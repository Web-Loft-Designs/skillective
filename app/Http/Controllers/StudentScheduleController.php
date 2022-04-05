<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;
use App\Repositories\LessonRepository;
use App\Repositories\GenreRepository;

class StudentScheduleController extends Controller
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
    public function index(LessonRepository $lessonRepository, GenreRepository $genreRepository)
    {
        $vars = [
            'page_title'	=> 'Schedule',
			'siteGenres'	=> $genreRepository->presentResponse($genreRepository->getSiteGenres())['data'],
			'userGenres'	=> $genreRepository->presentResponse(Auth::user()->genres)['data'],
        ];

        return view('frontend.student.schedule', $vars);
    }

}