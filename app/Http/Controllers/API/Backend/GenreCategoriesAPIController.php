<?php

namespace App\Http\Controllers\API\Backend;

use App\Models\GenreCategory;
use App\Repositories\GenreCategoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;
use Auth;


class GenreCategoriesAPIController extends AppBaseController
{
    /** @var  GenreCategoryRepository */
    private $genreCategoryRepository;

    public function __construct(GenreCategoryRepository $genreCategoryRepo)
    {
        $this->genreCategoryRepository = $genreCategoryRepo;
    }

    public function index(Request $request)
    {
		$genreCategories = $this->genreCategoryRepository->presentResponse( $this->genreCategoryRepository->getGenreCategories($request) );

        return $this->sendResponse($genreCategories);
    }

	public function delete(GenreCategory $category)
	{
		$category->delete();
		return $this->sendResponse(true, 'Genre Category deleted');
	}
}