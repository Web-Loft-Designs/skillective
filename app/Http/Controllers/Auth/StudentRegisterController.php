<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Prettus\Repository\Exceptions\RepositoryException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repositories\GenreRepository;
use App\Repositories\InvitationRepository;

class StudentRegisterController extends Controller
{
    /**
     * @param Request $request
     * @param GenreRepository $genreRepository
     * @param InvitationRepository $invitationRepository
     * @return Application|Factory|View|never
     * @throws RepositoryException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
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
	}
}
