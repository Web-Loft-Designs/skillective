<?php

namespace App\Repositories;

use App\Models\Faq;
use App\Models\FaqCategory;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use App\Criteria\FaqSearchCriteria;
use App\Criteria\FaqFilterByCategoryCriteria;
use Cookie;
use DB;

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
     * Configure the Model
     **/
    public function model()
    {
        return Faq::class;
    }

	/**
	 * @var bool
	 */
	protected $skipPresenter = true;

	public function presenter() {
		return "Prettus\\Repository\\Presenter\\ModelFractalPresenter";
	}

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
	public function getSiteFaqs(){
		return $this->scopeQuery(function($query){
			return $query->orderBy('position','asc')->orderBy('title','asc');
		})->with(['category'])->get();
	}

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
		}
		ksort($categorized);
		return $categorized;
	}

    public function presentResponse($data){
        return $this->presenter->present($data);
	}
}
