<?php

namespace App\Http\Controllers\API;

use App\Repositories\GenreRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;


class GenreAPIController extends AppBaseController
{
    /** @var  GenreRepository */
    private $genreRepository;


    public function __construct(GenreRepository $genreRepo)
    {
        parent::__construct();
        $this->genreRepository = $genreRepo;
    }


    /**
     * @param Request $request
     * @return mixed
     * @throws RepositoryException
     */
    public function index(Request $request)
    {
        $this->genreRepository->pushCriteria(new RequestCriteria($request));
        $this->genreRepository->pushCriteria(new LimitOffsetCriteria($request));
        $genres = $this->genreRepository->orderBy('title')->all();

        return $this->genreRepository->presentResponse($genres)['data'];
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function categorizedGenres(Request $request)
	{
		return $this->genreRepository->presentResponse($this->genreRepository->getCategorizedGenres())['data'];
	}

    /**
     * @param Request $request
     * @return mixed
     * @throws RepositoryException
     */
    public function featured(Request $request)
	{
		$genres = $this->genreRepository->getFeatured();

		return $this->genreRepository->presentResponse($genres)['data'];
	}

}
