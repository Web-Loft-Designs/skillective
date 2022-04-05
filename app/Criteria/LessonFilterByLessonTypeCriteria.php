<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class LessonFilterByLessonTypeCriteria.
 *
 * @package namespace App\Criteria;
 */
class LessonFilterByLessonTypeCriteria implements CriteriaInterface
{
	protected $lesson_type;

	public function __construct($lesson_type)
	{
		$this->lesson_type = $lesson_type;
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
		$model = $model->where("lessons.lesson_type", $this->lesson_type);
        return $model;
    }
}
