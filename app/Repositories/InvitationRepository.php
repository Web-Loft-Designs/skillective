<?php

namespace App\Repositories;

use App\Criteria\InvitationSearchCriteria;
use App\Models\Invitation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use InfyOm\Generator\Common\BaseRepository;
use Cookie;

class InvitationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'invitation_token'
    ];

    /**
     * @var bool
     */
    protected $skipPresenter = true;
    /**
     * Configure the Model
     **/
    public function model()
    {
        return Invitation::class;
    }

    public function presenter()
    {
        return "Prettus\\Repository\\Presenter\\ModelFractalPresenter";
    }

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

    public function getInvitations(Request $request, $roleID)
    {

        if ($request->filled('s'))
            $this->pushCriteria(new InvitationSearchCriteria($request->get('s')));

        $defaultPerPage = 25;
        $perPage = $defaultPerPage;
        if ($roleID == 2)
            $perPage = Cookie::get('adminInstructorsPerPage', $defaultPerPage);
        elseif ($roleID == 3)
            $perPage = Cookie::get('adminStudentsPerPage', $defaultPerPage);

        if ($request->filled('limit') && $request->input('limit') > 0)
            return $this->paginate($request->input('limit'), ['invitations.*']);
        else
            return $this->paginate($perPage, ['invitations.*']);

    }

    public function presentResponse($data)
    {
        $data = $this->presenter->present($data);

        return $data;
    }

}
