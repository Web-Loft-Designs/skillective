<?php

namespace App\Repositories;

use App\Models\Invitation;
use Carbon\Carbon;
use InfyOm\Generator\Common\BaseRepository;

class InvitationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'invitation_token'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Invitation::class;
    }

	/**
	 * @var bool
	 */
	protected $skipPresenter = true;

	public function findUserInvitation($invitation_token){
		return $this->findWhere(
			[
				'invitation_token' => $invitation_token,
				'invited_user_id' => null
			]
		)->first();
	}

	public function getAverageInvitedInstructors(){
		$firstInvitation = $this->model->orderBy('created_at', 'asc')->first();
		$countMonths = Carbon::now()->diffInMonths($firstInvitation->created_at);
		$totalInvites = $this->model->where('invited_as_instructor', 1)->count();
		return $countMonths>0 ? number_format($totalInvites / $countMonths, 1) : $totalInvites;
	}
}
