<?php

namespace App\Criteria;

use App\Models\User;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class UserActiveCriteria implements CriteriaInterface
{
	public function __construct() {}

    public function apply($model, RepositoryInterface $repository)
    {
		$model = $model->where('users.status', User::STATUS_ACTIVE);
        return $model;
    }
}
