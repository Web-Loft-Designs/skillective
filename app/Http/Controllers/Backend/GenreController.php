<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\CreateGenreRequest;
use App\Http\Requests\UpdateGenreRequest;
use App\Repositories\GenreCategoryRepository;
use App\Repositories\GenreRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\Input;

class GenreController extends AppBaseController
{
    /** @var  GenreRepository */
    private $genreRepository;

    public function __construct(GenreRepository $genreRepo)
    {
        $this->genreRepository = $genreRepo;
		parent::__construct();
    }

    /**
     * Display a listing of the Genre.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $genres = $this->genreRepository->presentResponse($this->genreRepository->getGenres($request));

        return view('backend.genres.index')
            ->with('genres', $genres);
    }

    /**
     * Show the form for creating a new Genre.
     *
     * @return Response
     */
    public function create( GenreCategoryRepository $categoryRepo )
    {
		$vars = [
			'categories' => $categoryRepo->all()
		];
        return view('backend.genres.create', $vars);
    }

    /**
     * Store a newly created Genre in storage.
     *
     * @param CreateGenreRequest $request
     *
     * @return Response
     */
    public function store(CreateGenreRequest $request)
    {
        $input = $request->except(['image']);

        $genre = $this->genreRepository->create($input);

		if ($request->hasFile('image')) {
			$genre->uploadImage( $request->file('image') );
		}

        Flash::success('Genre saved.');

        return redirect(route('backend.genres.index'));
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
        $genre = $this->genreRepository->findWithoutFail($id);

        if (empty($genre)) {
            Flash::error('Genre not found');

            return redirect(route('backend.genres.index'));
        }

        return view('backend.genres.show')->with('genre', $genre);
    }

    /**
     * Show the form for editing the specified Genre.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id, GenreCategoryRepository $categoryRepo)
    {
        $genre = $this->genreRepository->findWithoutFail($id);

        if (empty($genre)) {
            Flash::error('Genre not found');

            return redirect(route('backend.genres.index'));
        }
		$vars = [
			'categories' => $categoryRepo->all(),
			'genre' => $genre
		];
        return view('backend.genres.edit', $vars);
    }

    /**
     * Update the specified Genre in storage.
     *
     * @param  int              $id
     * @param UpdateGenreRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateGenreRequest $request)
    {
        $genre = $this->genreRepository->findWithoutFail($id);

        if (empty($genre)) {
            Flash::error('Genre not found');

            return redirect(route('backend.genres.index'));
        }

		if ($request->hasFile('image')) {
			$genre->deleteOldImage();
			$genre->uploadImage( $request->file('image') );
		}

		$input = $request->except(['image']);
        $this->genreRepository->update($input, $id);

        Flash::success('Genre updated.');

        return redirect(route('backend.genres.index'));
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
		Flash::danger('Genre can\'t be deleted.');

        return redirect(route('backend.genres.index'));
//        $genre = $this->genreRepository->findWithoutFail($id);
//
//        if (empty($genre)) {
//            Flash::error('Genre not found');
//
//            return redirect(route('backend.genres.index'));
//        }
//
//		$this->genreRepository->deleteOldImage($genre->image);
//        $this->genreRepository->delete($id);
//
//        Flash::success('Genre deleted successfully.');
//
//        return redirect(route('backend.genres.index'));
    }
}
