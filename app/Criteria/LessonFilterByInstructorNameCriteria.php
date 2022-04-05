<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class LessonFilterByInstructorNameCriteria.
 *
 * @package namespace App\Criteria;
 */
class LessonFilterByInstructorNameCriteria implements CriteriaInterface
{
	protected $instructor_name;

	public function __construct($instructor_name)
	{
		$this->instructor_name = $instructor_name;
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
		$model = $model->whereRaw("CONCAT(users.first_name, ' ', users.last_name) LIKE '%".escape_like($this->instructor_name)."%'");
        return $model;
    }
}
