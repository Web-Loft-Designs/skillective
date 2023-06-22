<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class LessonFilterByGenreCriteria.
 *
 * @package namespace App\Criteria;
 */
class PreRLessonFilterByGenreCriteria implements CriteriaInterface
{
	protected $genre_id;

	public function __construct($genre_id)
	{
		$this->genre_id = $genre_id;
	}

    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $model = $model->where("pre_r_lessons.genre_id", $this->genre_id);
        return $model;
    }
}
