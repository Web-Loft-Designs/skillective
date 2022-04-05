<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\CreateGenreCategoryRequest;
use App\Http\Requests\UpdateGenreCategoryRequest;
use App\Repositories\GenreCategoryRepository;
use App\Repositories\GenreRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;

class GenreCategoryController extends AppBaseController
{
    /** @var  GenreCategoryRepository */
    private $genreCategoryRepository;

    public function __construct(GenreCategoryRepository $genreCategoryRepo)
    {
        $this->genreCategoryRepository = $genreCategoryRepo;
		parent::__construct();
    }

    /**
     * Display a listing of the GenreCategory.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $genreCategories = $this->genreCategoryRepository->presentResponse($this->genreCategoryRepository->getGenreCategories($request));
        return view('backend.genreCategories.index', ['genreCategories' => $genreCategories, 'page_title'=>'Genre Categories List']);
    }

    /**
     * Show the form for creating a new Genre.
     *
     * @return Response
     */
    public function create()
    {
        return view('backend.genreCategories.create', ['page_title'=>'Create Genre Category']);
    }

    /**
     * Store a newly created Genre in storage.
     *
     * @param CreateGenreRequest $request
     *
     * @return Response
     */
    public function store(CreateGenreCategoryRequest $request)
    {
        $input = $request->all();

        $genre = $this->genreCategoryRepository->create($input);

        Flash::success('Genre Category saved.');

        return redirect(route('backend.genre-categories.index'));
    }

    /**
     * Display the specified Genre.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $genre = $this->genreCategoryRepository->findWithoutFail($id);

        if (empty($genre)) {
            Flash::error('Genre Category not found');

            return redirect(route('backend.genre-categories.index'));
        }

        return view('backend.genreCategories.show')->with('genre', $genre);
    }

    /**
     * Show the form for editing the specified Genre.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $genreCategory = $this->genreCategoryRepository->findWithoutFail($id);

        if (empty($genreCategory)) {
            Flash::error('Genre Category not found');

            return redirect(route('backend.genre-categories.index'));
        }
		$vars = [
			'genreCategory' => $genreCategory,
			'page_title'=>'Edit Genre Category'
		];
        return view('backend.genreCategories.edit', $vars);
    }

    /**
     * Update the specified Genre in storage.
     *
     * @param  int              $id
     * @param UpdateGenreRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateGenreCategoryRequest $request)
    {
        $genre = $this->genreCategoryRepository->findWithoutFail($id);

        if (empty($genre)) {
            Flash::error('Genre Category not found');

            return redirect(route('backend.genre-categories.index'));
        }

		$input = $request->all();
        $this->genreCategoryRepository->update($input, $id);

        Flash::success('Genre Category updated.');

        return redirect(route('backend.genre-categories.index'));
    }

    /**
     * Remove the specified Genre from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
		Flash::danger('Genre Category can\'t be deleted.');

        return redirect(route('backend.genre-categories.index'));
//        $genre = $this->genreCategoryRepository->findWithoutFail($id);
//
//        if (empty($genre)) {
//            Flash::error('Genre not found');
//
//            return redirect(route('backend.genre-categories.index'));
//        }
//
//		$this->genreCategoryRepository->deleteOldImage($genre->image);
//        $this->genreCategoryRepository->delete($id);
//
//        Flash::success('Genre deleted successfully.');
//
//        return redirect(route('backend.genre-categories.index'));
    }
}
