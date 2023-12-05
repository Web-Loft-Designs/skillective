<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\CreateGenreRequest;
use App\Http\Requests\UpdateGenreRequest;
use App\Repositories\GenreCategoryRepository;
use App\Repositories\GenreRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Laracasts\Flash\Flash;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;

class GenreController extends AppBaseController
{
    /** @var  GenreRepository */
    private GenreRepository $genreRepository;

    public function __construct(GenreRepository $genreRepo)
    {
        $this->genreRepository = $genreRepo;
		parent::__construct();
    }

    /**
     * Display a listing of the Genre.
     *
     * @param Request $request
     * @return View
     * @throws RepositoryException
     */
    public function index(Request $request): View
    {
        $genres = $this->genreRepository->presentResponse($this->genreRepository->getGenres($request));

        return view('backend.genres.index',['genres' => $genres]);
    }

    /**
     * Show the form for creating a new Genre.
     *
     * @param GenreCategoryRepository $categoryRepo
     * @return View
     */
    public function create( GenreCategoryRepository $categoryRepo ): View
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
     * @return Application|RedirectResponse|Redirector
     * @throws ValidatorException
     */
    public function store(CreateGenreRequest $request): Redirector|RedirectResponse|Application
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
     * @param int $id
     *
     * @return View
     */
    public function show($id): View
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

    }
}
