<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class GenreFilterByCategoryCriteria implements CriteriaInterface
{
	protected $categoryId;

	public function __construct($categoryId)
	{
		$this->categoryId = (int)$categoryId;
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
		$model = $model->where('genres.genre_category_id', '=', $this->categoryId);
        return $model;
    }
}
