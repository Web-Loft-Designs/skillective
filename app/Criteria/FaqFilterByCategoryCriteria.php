<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class FaqFilterByCategoryCriteria implements CriteriaInterface
{
	protected $categoryId;

	public function __construct($categoryId)
	{
		$this->categoryId = (int)$categoryId;
	}
    
    public function apply($model, RepositoryInterface $repository)
    {
		$model = $model->where('faqs.faq_category_id', '=', $this->categoryId);
        return $model;
    }
}
