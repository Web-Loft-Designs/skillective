<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class UserFilterByInstagranHandleCriteria implements CriteriaInterface
{
	protected $instagram_handle;

	public function __construct($instagram_handle)
	{
		$this->instagram_handle = $instagram_handle;
	}
    public function apply($model, RepositoryInterface $repository)
    {
		$model = $model->where("profiles.instagram_handle", 'like', '%' . ltrim($this->instagram_handle, '@') . '%');
        return $model;
    }
}
