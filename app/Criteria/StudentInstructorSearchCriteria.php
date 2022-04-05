<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class LessonFilterByInstructorNameCriteria.
 *
 * @package namespace App\Criteria;
 */
class StudentInstructorSearchCriteria implements CriteriaInterface
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
		$model	= $model->whereRaw("( CONCAT(first_name, ' ', last_name) $sLike OR users.email $sLike OR profiles.instagram_handle $sLike OR profiles.mobile_phone $sLike )");
        return $model;
    }
}
