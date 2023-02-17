<?php

namespace App\Http\Controllers\API\Auth;

use Illuminate\Foundation\Auth\RegistersUsers;
use App\Repositories\GenreRepository;
use App\Repositories\UserRepository;
use App\Repositories\InvitationRepository;
use App\Http\Requests\API\StudentRegisterRequest;
use App\Http\Controllers\AppBaseController;
use App\Facades\UserRegistrator;
use Illuminate\Http\JsonResponse;

class StudentRegisterController extends AppBaseController
{
	use RegistersUsers;

	private $genreRepository = null;

	/** @var  UserRepository */
	private $userRepository;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct( UserRepository $userRepo, GenreRepository $genre_repository )
	{
		$this->middleware('guest');
		$this->userRepository = $userRepo;
		$this->genreRepository = $genre_repository;
        parent::__construct();
	}


    /**
     * @param StudentRegisterRequest $request
     * @param InvitationRepository $invitationRepository
     * @return JsonResponse
     */
    public function register(StudentRegisterRequest $request, InvitationRepository $invitationRepository) {

		$invitation = null;
		if ( $request->filled( 'invitation' )
			 && ( $invitation = $invitationRepository->findUserInvitation($request->input( 'invitation' )) ) == null
		) {
			return $this->sendError("Invitation not found");
		}
		$student = UserRegistrator::registerInactiveStudent($request);
		return $this->sendResponse(true, 'Check you email to finish registration and activate your account');

	}
}
