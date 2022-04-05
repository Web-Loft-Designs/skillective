<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class BackendBookingSearchCriteria.
 *
 * @package namespace App\Criteria;
 */
class BackendBookingSearchCriteria implements CriteriaInterface
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
		$model	= $model->whereRaw("( CONCAT(instructors.first_name, ' ', instructors.last_name) $sLike OR CONCAT(students.first_name, ' ', students.last_name) $sLike )");
        return $model;
    }
}
