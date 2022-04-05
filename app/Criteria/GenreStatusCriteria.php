<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class GenreStatusCriteria implements CriteriaInterface
{
	protected $status;

	public function __construct($status)
	{
		$this->status = $status;
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
		switch ($this->status){
			case 'disabled':
				$model = $model->where('genres.is_disabled', '=', 1);
				break;
			case 'active':
			default:
				$model = $model->where('genres.is_disabled', '=', 0);
				break;
		}
        return $model;
    }
}
