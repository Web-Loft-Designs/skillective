<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\CreateProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Repositories\UserRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Response;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Log;

class InstructorController extends AppBaseController
{

	/** @var  UserRepository */
	private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
		parent::__construct();
    }

    /**
     * Display a listing of the User.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
		try{
			if (!$request->has('status'))
				$request->request->add([
					'status'	=> 'active'
				]);
			$roleID = Role::findByName(User::ROLE_INSTRUCTOR)->id;
			$this->userRepository->setPresenter("App\\Presenters\\InstructorsInListPresenter");
			$instructors = $this->userRepository->presentResponse($this->userRepository->getUsers($request, $roleID));
		}catch (\Exception $e){
			Log::error('getInstructors : ' . $e->getMessage());
			$instructors = ['data'=>[]];
		}

		Log::info($instructors);

		$invitedByUser = null;
		if ($request->has('invited_by')){
			$this->userRepository->resetCriteria();
			$invitedByUser = $this->userRepository->findWithoutFail((int)$request->input('invited_by'));
		}

		$vars = [
			'instructors' => $instructors,
			'page_title' => "Instructors",
		];
		if ($invitedByUser)
			$vars['invitedByInstagramHandle'] = $invitedByUser->profile->instagram_handle ? ('@'.$invitedByUser->profile->instagram_handle) : $invitedByUser->getName();

		return view('backend.instructors.index', $vars);
    }
}
