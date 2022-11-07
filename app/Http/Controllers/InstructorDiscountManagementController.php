<?php

namespace App\Http\Controllers;

use Auth;
use Session;
use App\Repositories\GenreRepository;
use Log;

class InstructorDiscountManagementController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(GenreRepository $genreRepository)
    {
        $vars = [
            'page_title' => 'Discount Management',
            'siteGenres'	=> $genreRepository->presentResponse($genreRepository->getSiteGenres())['data'],
            'userGenres'	=> $genreRepository->presentResponse(Auth::user()->genres)['data'],
            'user' => Auth::user(),
        ];

        return view('frontend.instructor.discount-management', $vars);
    }

}