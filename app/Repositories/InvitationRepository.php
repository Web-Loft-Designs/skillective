<?php

namespace App\Repositories;

use App\Criteria\InvitationSearchCriteria;
use App\Models\Invitation;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Prettus\Repository\Exceptions\RepositoryException;

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
     * @return string
     */
    public function model()
    {
        return Invitation::class;
    }

    /**
     * @return string
     */
    public function presenter()
    {
        return "Prettus\\Repository\\Presenter\\ModelFractalPresenter";
    }

    /**
     * @param $invitation_token
     * @return Invitation
     */
    public function findUserInvitation($invitation_token): Invitation
    {
		return $this->findWhere(
			[
				'invitation_token' => $invitation_token,
				'invited_user_id' => null
			]
		)->first();
	}

    /**
     * @return string
     */
    public function getAverageInvitedInstructors(){
		$firstInvitation = $this->model->orderBy('created_at', 'asc')->first();
		$countMonths = Carbon::now()->diffInMonths($firstInvitation->created_at);
		$totalInvites = $this->model->where('invited_as_instructor', 1)->count();
		return $countMonths>0 ? number_format($totalInvites / $countMonths, 1) : $totalInvites;
	}

    /**
     * @param Request $request
     * @param $roleID
     * @return LengthAwarePaginator|Collection|mixed
     * @throws RepositoryException
     */
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

    /**
     * @param $data
     * @return mixed
     */
    public function presentResponse($data)
    {
        $data = $this->presenter->present($data);

        return $data;
    }

}
