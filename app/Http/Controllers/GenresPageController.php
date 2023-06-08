<?php

namespace App\Http\Controllers;


use App\Repositories\GenreRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class GenresPageController extends Controller
{

    /**
     * @param GenreRepository $genreRepository
     * @return Application|Factory|View
     */
    public function index(GenreRepository $genreRepository)
    {
    	$vars = [
			'genres' => $genreRepository->getSiteGenres()
		];
        return view('genres', $vars);
    }
}
