<?php

namespace App\Http\Controllers\API\Backend;

use App\Models\GenreCategory;
use App\Repositories\GenreCategoryRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Prettus\Repository\Exceptions\RepositoryException;

class GenreCategoriesAPIController extends AppBaseController
{
    /** @var  GenreCategoryRepository */
    private $genreCategoryRepository;

    public function __construct(GenreCategoryRepository $genreCategoryRepo)
    {
        $this->genreCategoryRepository = $genreCategoryRepo;
        parent::__construct();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws RepositoryException
     */
    public function index(Request $request)
    {
		$genreCategories = $this->genreCategoryRepository->presentResponse( $this->genreCategoryRepository->getGenreCategories($request) );
        return $this->sendResponse($genreCategories);
    }

    /**
     * @param GenreCategory $category
     * @return JsonResponse
     */
    public function delete(GenreCategory $category)
	{
		$category->delete();
		return $this->sendResponse(true, 'Genre Category deleted');
	}
}