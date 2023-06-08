<?php

namespace App\Http\Controllers\API\Backend;

use App\Models\Genre;
use App\Repositories\GenreRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Prettus\Repository\Exceptions\RepositoryException;


class GenresAPIController extends AppBaseController
{
    /** @var  GenreRepository */
    private $genreRepository;

    public function __construct(GenreRepository $genreRepo)
    {
        $this->genreRepository = $genreRepo;
        parent::__construct();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws RepositoryException
     */
    public function index(Request $request)
    {
		$genres = $this->genreRepository->presentResponse( $this->genreRepository->getGenres($request) );
        return $this->sendResponse($genres);
    }

    /**
     * @param Genre $genre
     * @return JsonResponse
     */
    public function disable(Genre $genre)
	{
		$genre->is_disabled = true;
		$genre->save();
		return $this->sendResponse(true, 'Genre disabled and won\'t be available when creating new lessons');
	}

    /**
     * @param Genre $genre
     * @return JsonResponse
     */
    public function enable(Genre $genre)
	{
		$genre->is_disabled = false;
		$genre->save();
		return $this->sendResponse(true, 'Genre enabled and will be available when creating new lessons');
	}
}
