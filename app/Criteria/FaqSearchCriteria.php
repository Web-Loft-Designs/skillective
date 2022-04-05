<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class FaqSearchCriteria implements CriteriaInterface
{
	protected $searchString;

	public function __construct($searchString)
	{
		$this->searchString = $searchString;
	}

    public function apply($model, RepositoryInterface $repository)
    {
		$sLike	= " LIKE '%" . escape_like($this->searchString) . "%' ";
		$model	= $model->whereRaw("faqs.title $sLike OR faqs.content $sLike");
        return $model;
    }
}
