<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use App\Models\User;

/**
 * Class LessonFilterByInstructorNameCriteria.
 *
 * @package namespace App\Criteria;
 */
class UserIdCriteria implements CriteriaInterface
{
	protected $userId;

	public function __construct($userId)
	{
		$this->userId = (int)$userId;
	}

    public function apply($model, RepositoryInterface $repository)
    {
		$model->where('users.id', '=', $this->userId);
        return $model;
    }
}
