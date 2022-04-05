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
class UserStatusCriteria implements CriteriaInterface
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
			case 'suspended':
				$model = $model->where('users.status', '=', User::STATUS_BLOCKED);
				break;
			case 'approved':
				$model = $model->where('users.status', '=', User::STATUS_APPROVED);
				break;
			case 'on_review':
				$model = $model->where('users.status', '=', User::STATUS_ON_REVIEW);
				break;
			case 'active':
			default:
				$model = $model->where('users.status', '=', User::STATUS_ACTIVE);
				break;
		}
        return $model;
    }
}
