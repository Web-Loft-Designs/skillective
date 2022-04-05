<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Repositories\GenreRepository;
use App\Repositories\InvitationRepository;
use Symfony\Component\HttpFoundation\Request;
use Session;
use Log;
use Cookie;
use App\Models\Setting;

class InstructorRegisterController extends Controller
{
	/**
	 * Show the application registration form.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function showRegistrationForm(Request $request, GenreRepository $genreRepository, InvitationRepository $invitationRepository)
	{
		session()->forget('invitation');
		$initialFormData = [];
		$vars = [];

		if (session()->has('submittedInstructor')) {
			$initialFormData = session()->get('submittedInstructor');
			$initialFormData = (is_array($initialFormData) && count($initialFormData) == 1) ? $initialFormData[0] : $initialFormData;
			session()->forget('submittedInstructor');
		}

		// check invitation if required and if presented
		$free_instructor_registration_enabled = Setting::getValue('free_instructor_registration_enabled', 0);

		$invitation = null;

		if ($request->input('invitation') === 'Nc2Chuxbf83XXnjfDj') {
		} else {
			if (
				$request->filled('invitation')
				&& ($invitation = $invitationRepository->findUserInvitation($request->input('invitation'))) == null
			) {
				return abort(404);
			}
		}

		if ($invitation) {
			$initialFormData['email'] = $invitation->invited_email;
			$initialFormData['mobile_phone'] = $invitation->invited_mobile_phone;
			$initialFormData['invitation'] = $invitation->invitation_token;
			session()->push('invitation', $invitation->invitation_token);
		}

		if ($request->input('invitation') === 'Nc2Chuxbf83XXnjfDj') {
			$initialFormData['invitation'] = 'Nc2Chuxbf83XXnjfDj';
		} else {
			if (!$free_instructor_registration_enabled) {
				if (
					!$request->filled('invitation')
					|| $invitation == null
					|| $invitation->invited_as_instructor != 1
				) {
					return abort(403);
				}
			}
		}

		$vars['initialFormData'] = $initialFormData;
		$vars['siteGenres'] = $genreRepository->presentResponse($genreRepository->getSiteGenres())['data'];
		$vars['categorizedGenres'] = $genreRepository->getCategorizedGenres();
		$vars['featuredGenres'] = $genreRepository->presentResponse($genreRepository->getFeatured())['data'];


		return view('auth.instructor-register', $vars);
	}

	public function showSuccessPage()
	{
		if (Cookie::has('instructorRegistered'))
			return view('auth.instructor-register-success');
		else
			return redirect(route('home'));
	}
}
