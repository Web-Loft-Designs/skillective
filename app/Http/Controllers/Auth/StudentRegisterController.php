<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Repositories\GenreRepository;
use App\Repositories\InvitationRepository;

class StudentRegisterController extends Controller
{
	public function showRegistrationForm(Request $request , GenreRepository $genreRepository, InvitationRepository $invitationRepository)
	{
		$initialFormData = [];
		$vars = [];

		if (session()->has('submittedStudent')){
			$initialFormData = session()->get('submittedStudent');
			$initialFormData = ( is_array($initialFormData) && count($initialFormData)==1 ) ? $initialFormData[0] : $initialFormData;
			session()->forget('submittedStudent');
		}

		$invitation = null;
		if ( $request->filled( 'invitation' )
			 && ( $invitation = $invitationRepository->findUserInvitation($request->input( 'invitation' )) ) == null
		) {
			return abort( 404 );
		}

		if($invitation){
			$initialFormData['email'] = $invitation->invited_email;
			$initialFormData['mobile_phone'] = $invitation->invited_mobile_phone;
			$initialFormData['invitation'] = $invitation->invitation_token;
		}

		$vars['initialFormData'] = $initialFormData;
		$vars['siteGenres'] = $genreRepository->presentResponse($genreRepository->getSiteGenres())['data'];
		$vars['categorizedGenres'] = $genreRepository->getCategorizedGenres();
		$vars['featuredGenres'] = $genreRepository->presentResponse($genreRepository->getFeatured())['data'];

		return view('auth.student-register', $vars);

//		return view('auth.student-register', ['registration_via_instagram_url' => Socialite::driver('instagram')->with(['redirect_uri' => route('social.instagram.student.registration')])->redirect()->getTargetUrl()]);
	}
}
