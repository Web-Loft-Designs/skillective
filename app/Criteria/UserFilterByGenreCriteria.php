<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class UserFilterByGenreCriteria implements CriteriaInterface
{
	protected $genre_id;

	public function __construct($genre_id)
	{
		$this->genre_id = $genre_id;
	}

    public function apply($model, RepositoryInterface $repository)
    {
		$model = $model->where("user_genre.genre_id", $this->genre_id);
        return $model;
    }
}
