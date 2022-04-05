<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\GenreRepository;

class GenresPageController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(GenreRepository $genreRepository)
    {
    	$vars = [
			'genres' => $genreRepository->getSiteGenres()
		];
        return view('genres', $vars);
    }
}
