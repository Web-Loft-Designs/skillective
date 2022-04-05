<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class LessonRequestSearchCriteria implements CriteriaInterface
{
	protected $searchString;

	public function __construct($searchString)
	{
		$this->searchString = $searchString;
	}

    public function apply($model, RepositoryInterface $repository)
    {
		$sLike	= " LIKE '%" . escape_like($this->searchString) . "%' ";
		$model	= $model->whereRaw("
			( CONCAT(users.first_name, ' ', users.last_name) $sLike OR users.email $sLike OR profiles.instagram_handle $sLike OR profiles.mobile_phone $sLike OR genres.title $sLike OR lesson_requests.location $sLike )
		");
        return $model;
    }
}
