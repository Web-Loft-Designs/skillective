<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class UserFilterByNameCriteria implements CriteriaInterface
{
	protected $user_name;
    
	public function __construct($user_name)
	{
		$this->user_name = $user_name;
	}
    
    public function apply($model, RepositoryInterface $repository)
    {
		$model = $model->whereRaw("CONCAT(users.first_name, ' ', users.last_name) LIKE '%".escape_like($this->user_name)."%'");
        return $model;
    }
}
