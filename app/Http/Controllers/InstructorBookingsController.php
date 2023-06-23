<?php

namespace App\Http\Controllers;

use App\Repositories\GenreRepository;
use Illuminate\Support\Facades\Auth;

class InstructorBookingsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(GenreRepository $genreRepository)
    {
		$vars = [
			'page_title' => 'Bookings',
			'bookings' => [], // disable
            'siteGenres'	=> $genreRepository->presentResponse($genreRepository->getSiteGenres())['data'],
            'userGenres'	=> $genreRepository->presentResponse(Auth::user()->genres)['data']
        ];

		return view('frontend.instructor.bookings', $vars);
    }
}
