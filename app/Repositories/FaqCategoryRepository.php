<?php

namespace App\Repositories;

use App\Models\FaqCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use App\Criteria\FaqCategorySearchCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

class FaqCategoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title'
    ];


    /**
     * @return string
     */
    public function model()
    {
        return FaqCategory::class;
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
     * @param $data
     * @return mixed
     */
    public function presentResponse($data){
		return $this->presenter->present($data);
	}

    /**
     * @param Request $request
     * @return LengthAwarePaginator|Collection|mixed
     * @throws RepositoryException
     */
    public function getFaqCategories(Request $request){
		$this->resetCriteria();
		$this->pushCriteria(new LimitOffsetCriteria($request));

		if ($request->filled('s'))
			$this->pushCriteria(new FaqCategorySearchCriteria($request->get('s')));

		$this->scopeQuery(function($query){
			return $query->addSelect('faq_categories.*')
						 ->addSelect(DB::raw('COUNT(faqs.id) as count_faqs'))
						 ->leftJoin('faqs', 'faq_categories.id', '=', 'faqs.faq_category_id')
						 ->orderBy('faq_categories.position','asc')
						 ->orderBy('faq_categories.title','asc')
						 ->groupBy('faq_categories.id');
		});

		$defaultPerPage = 25;
		$perPage = Cookie::get('adminFaqCategoriesPerPage', $defaultPerPage);

		return $this->paginate($perPage, ['faq_categories.*', 'count_faqs']);
	}
}
