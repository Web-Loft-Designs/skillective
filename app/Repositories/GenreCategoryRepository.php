<?php

namespace App\Repositories;

use App\Models\GenreCategory;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use App\Criteria\GenreCategorySearchCriteria;
use Cookie;
use DB;

class GenreCategoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return GenreCategory::class;
    }

	/**
	 * @var bool
	 */
	protected $skipPresenter = true;

	public function presenter() {
		return "Prettus\\Repository\\Presenter\\ModelFractalPresenter";
	}

	public function presentResponse($data){
		return $this->presenter->present($data);
	}

	public function getGenreCategories(Request $request){
		$this->resetCriteria();
		$this->pushCriteria(new LimitOffsetCriteria($request));

		if ($request->filled('s'))
			$this->pushCriteria(new GenreCategorySearchCriteria($request->get('s')));

		$this->scopeQuery(function($query){
			return $query->addSelect('genre_categories.*')
						 ->addSelect(DB::raw('COUNT(genres.id) as count_genres'))
						 ->leftJoin('genres', 'genre_categories.id', '=', 'genres.genre_category_id')
						 ->whereRaw('(`genres`.`is_disabled` = 0 OR `genres`.`is_disabled` IS NULL)')
						 ->orderBy('genre_categories.title','asc')
						 ->groupBy('genre_categories.id');
		});

		$defaultPerPage = 25;
		$perPage = Cookie::get('adminGenreCategoriesPerPage', $defaultPerPage);

		return $this->paginate($perPage, ['genre_categories.*', 'count_genres']);
	}
}
