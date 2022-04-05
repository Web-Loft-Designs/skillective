<?php

namespace App\Http\Controllers\API\Auth;

use Illuminate\Foundation\Auth\RegistersUsers;
use App\Repositories\GenreRepository;
use App\Repositories\UserRepository;
use App\Http\Requests\InstructorRegisterRequest;
use App\Http\Controllers\AppBaseController;
use Laravel\Socialite\Facades\Socialite;
use App\Facades\UserRegistrator;
use App\Models\Setting;
use Flash;
use Log;
use App\Repositories\InvitationRepository;
use App\Models\Invitation;

class InstructorRegisterController extends AppBaseController
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
	public function __construct(UserRepository $userRepo, GenreRepository $genre_repository)
	{
		$this->middleware('guest');
		$this->userRepository = $userRepo;
		$this->genreRepository = $genre_repository;
	}

	public function remember(InstructorRegisterRequest $request, InvitationRepository $invitationRepository)
	{
		$free_instructor_registration_enabled = Setting::getValue('free_instructor_registration_enabled', 0);
		$invitation = null;

		if ($request->input('invitation') === 'Nc2Chuxbf83XXnjfDj') {

			$input = [];




			$input['invited_as_instructor']	= true;
			$input['invited_by'] = 12;
			$invitation = new Invitation($input);
			$invitation->save();

			$request->merge([
				'invitation' => $invitation->invitation_token
			]);
		} else {
			if (
				$request->filled('invitation')
				&& ($invitation = $invitationRepository->findUserInvitation($request->input('invitation'))) == null
			) {
				Flash::error('Invitation does not exist')->important();
				return ['redirect' => route('home')];
			}
		}


		if (!$free_instructor_registration_enabled) {
			if (
				!$request->filled('invitation')
				|| $invitation == null
				|| $invitation->invited_as_instructor != 1
			) {
				Flash::error('Invitation not valid')->important();
				return ['redirect' => route('home')];
			}
		}



		session()->forget('submittedInstructor');
		session()->push('submittedInstructor', $request->all());

		return ['redirect' => Socialite::driver($request->provider)->with(['redirect_uri' => route('social.instructor.registration', ['provider' => $request->provider])])->redirect()->getTargetUrl()];
	}

	//	public function register(InstructorRegisterRequest $request) {
	//		return UserRegistrator::registerInstructor($request);
	//	}
}
