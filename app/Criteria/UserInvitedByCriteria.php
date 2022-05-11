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
class UserInvitedByCriteria implements CriteriaInterface
{
	protected $invitedByUserId;

	public function __construct($invitedByUserId)
	{
		$this->invitedByUserId = (int)$invitedByUserId;
	}

    public function apply($model, RepositoryInterface $repository)
    {
		$model->where('invitations.invited_by', '=', $this->invitedByUserId)->whereNull('invitations.deleted_at');
        return $model;
    }
}
