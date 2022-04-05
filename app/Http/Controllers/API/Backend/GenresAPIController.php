<?php

namespace App\Http\Controllers\API\Backend;

use App\Models\Genre;
use App\Repositories\GenreRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;
use Auth;


class GenresAPIController extends AppBaseController
{
    /** @var  GenreRepository */
    private $genreRepository;

    public function __construct(GenreRepository $genreRepo)
    {
        $this->genreRepository = $genreRepo;
    }

    public function index(Request $request)
    {
		$genres = $this->genreRepository->presentResponse( $this->genreRepository->getGenres($request) );

        return $this->sendResponse($genres);
    }

	public function disable(Genre $genre)
	{
		$genre->is_disabled = true;
		$genre->save();
		return $this->sendResponse(true, 'Genre disabled and won\'t be available when creating new lessons');
	}

    public function enable(Genre $genre)
	{
		$genre->is_disabled = false;
		$genre->save();
		return $this->sendResponse(true, 'Genre enabled and will be available when creating new lessons');
	}
}
