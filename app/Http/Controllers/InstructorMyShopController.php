<?php

namespace App\Http\Controllers;

use Auth;
use Session;
use App\Repositories\GenreRepository;
use Log;

class InstructorMyShopController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(GenreRepository $genreRepository)
    {
        $vars = [
            'page_title' => 'My Shop',
            'siteGenres'	=> $genreRepository->presentResponse($genreRepository->getSiteGenres())['data'],
            'userGenres'	=> $genreRepository->presentResponse(Auth::user()->genres)['data']
        ];

        return view('frontend.instructor.my-shop', $vars);
    }

}
