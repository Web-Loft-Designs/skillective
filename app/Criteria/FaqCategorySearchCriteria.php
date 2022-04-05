<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class FaqCategorySearchCriteria implements CriteriaInterface
{
	protected $searchString;

	public function __construct($searchString)
	{
		$this->searchString = $searchString;
	}

    public function apply($model, RepositoryInterface $repository)
    {
		$sLike	= " LIKE '%" . escape_like($this->searchString) . "%' ";
		$model	= $model->whereRaw("faq_categories.title $sLike");
        return $model;
    }
}
