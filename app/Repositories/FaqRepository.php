<?php

namespace App\Repositories;

use App\Models\Faq;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use App\Criteria\FaqSearchCriteria;
use App\Criteria\FaqFilterByCategoryCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

class FaqRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        'content'
    ];


    /**
     * @return string
     */
    public function model()
    {
        return Faq::class;
    }

	/**
	 * @var bool
	 */
	protected $skipPresenter = true;

    /**
     * @return string
     */
    public function presenter() {
		return "Prettus\\Repository\\Presenter\\ModelFractalPresenter";
	}

    /**
     * @param Request $request
     * @return LengthAwarePaginator|Collection|mixed
     * @throws RepositoryException
     */
    public function getFaqs(Request $request){
		$this->resetCriteria();
		$this->pushCriteria(new LimitOffsetCriteria($request));

		if ($request->filled('s'))
			$this->pushCriteria(new FaqSearchCriteria($request->get('s')));

		if ($request->has('category')){
			$this->pushCriteria(new FaqFilterByCategoryCriteria($request->get('category')));
		}

		$this->scopeQuery(function($query){
			return $query->orderBy('title','asc');
		});

		$defaultPerPage = 25;
		$perPage = Cookie::get('adminFaqsPerPage', $defaultPerPage);

		$this->with(['category']);

		return $this->paginate($perPage, ['faqs.*']);
	}

	// all faqs for frontend

    /**
     * @return LengthAwarePaginator|Collection|mixed
     */
    public function getSiteFaqs(){
		return $this->scopeQuery(function($query){
			return $query->orderBy('position','asc')->orderBy('title','asc');
		})->with(['category'])->get();
	}

    /**
     * @return array
     */
    public function getCategorizedFaqs(){
		$this->scopeQuery(function($query){
			return $query->orderBy('position','asc')->orderBy('title','asc');
		});

		$categorized = [];
		$faqs = $this->with('category')->all();
		foreach($faqs as $faq){
			$category = $faq->category? $faq->category->title : 'Uncategorized';
			if (!isset($categorized[$category]))
				$categorized[$category] = [];
			$categorized[$category][] = $faq;
		}ksort($categorized);
		return $categorized;
	}

    /**
     * @param $data
     * @return mixed
     */
    public function presentResponse($data){
        return $this->presenter->present($data);
	}
}
