<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Repositories\GenreRepository;
use App\Repositories\InvitationRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\Setting;

class InstructorRegisterController extends Controller
{

        //  цей метод формує дані для форми реєстрації і показує її  в blade
    public function showRegistrationForm(Request $request, GenreRepository $genreRepository, InvitationRepository $invitationRepository): View|Factory|Application
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
            //  ????  що за хрень ,,,???
		} else {
			if ($request->filled('invitation') && ($invitation = $invitationRepository->findUserInvitation($request->input('invitation'))) == null ) {
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

}
