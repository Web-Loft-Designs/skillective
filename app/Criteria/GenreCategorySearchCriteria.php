<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class GenreCategorySearchCriteria implements CriteriaInterface
{
	protected $searchString;

	public function __construct($searchString)
	{
		$this->searchString = $searchString;
	}

    /**
     * Apply criteria in query repository
     *
     * @param string              $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
		$sLike	= " LIKE '%" . escape_like($this->searchString) . "%' ";
		$model	= $model->whereRaw("genre_categories.title $sLike");
        return $model;
    }
}
