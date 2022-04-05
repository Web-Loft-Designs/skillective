<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateGenreAPIRequest;
use App\Http\Requests\API\UpdateGenreAPIRequest;
use App\Models\Genre;
use App\Repositories\GenreRepository;
use App\Repositories\GenreCategoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class GenreController
 * @package App\Http\Controllers\API
 */

class GenreAPIController extends AppBaseController
{
    /** @var  GenreRepository */
    private $genreRepository;

	/** @var  GenreCategoryRepository */
	private $genreCategoryRepository;

    public function __construct(GenreRepository $genreRepo, GenreCategoryRepository $genreCategoryRepo)
    {
        $this->genreRepository = $genreRepo;
        $this->genreCategoryRepository = $genreCategoryRepo;
    }

    /**
     * Display a listing of the Genre.
     * GET|HEAD /genres
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->genreRepository->pushCriteria(new RequestCriteria($request));
        $this->genreRepository->pushCriteria(new LimitOffsetCriteria($request));
        $genres = $this->genreRepository->orderBy('title')->all();

        return $this->genreRepository->presentResponse($genres)['data'];
    }

	public function categorizedGenres(Request $request)
	{
		return $this->genreRepository->presentResponse($this->genreRepository->getCategorizedGenres())['data'];
	}

	public function featured(Request $request)
	{
		$genres = $this->genreRepository->getFeatured();

		return $this->genreRepository->presentResponse($genres)['data'];
	}

}
