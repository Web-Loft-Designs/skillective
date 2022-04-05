<?php

namespace App\Criteria;

use App\Models\User;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class LessonOfActiveInstructorsCriteria.
 *
 * @package namespace App\Criteria;
 */
class LessonOfActiveInstructorsCriteria implements CriteriaInterface
{
	public function __construct() {}

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
		$model = $model->where('users.status', User::STATUS_ACTIVE);
        return $model;
    }
}
