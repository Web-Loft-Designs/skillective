<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Laracasts\Flash\Flash;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use App\Repositories\SocialRepository;
use App\Http\Requests\InstructorRegisterRequest;
use App\Http\Requests\API\StudentRegisterRequest;
use App\Facades\UserRegistrator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Http\Controllers\AppBaseController;
use Faker;
use App\Repositories\InvitationRepository;

class SocialController extends AppBaseController
{
	const INSTAGRAM_ALREADY_CONNECTED_ERROR = 'Your {provider} account already connected to some account on our website';

	/** @var  SocialRepository */
	private $socialRepository;
	private $defaultProvider = 'ig';

	public function __construct(SocialRepository $socialRepo)
	{
        parent::__construct();
		$this->socialRepository = $socialRepo;
	}


    /**
     * @param $provider
     * @return Application|Factory|View|RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function getSocialRedirect($provider)
    {
        $providerKey = Config::get('services.'.$provider);
        if (empty($providerKey)) {
            return view('frontend.pages.status')
                ->with('error', 'No such provider.');
        }

        return Socialite::driver($provider)->redirect();
    }


    /**
     * @param $provider
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function getSocialHandle($provider, Request $request)
    {
        if ($request->get('denied') != '') {
			Flash::error('You did not share your profile data with our social app.')->important();
            return redirect()->route('frontend.login');
        }
        try{
            $socialUserObject = Socialite::driver($provider)->user();
			$currentUser = Auth::user();
            if ($currentUser){ // attach social
				if ($socialUserObject->id && $provider) {
					$route = route('profile.edit');
					if ($this->checkUniqueSocialmediaProfile($socialUserObject->id, $provider)){
						Flash::error( str_replace('{provider}', ucfirst($provider), self::INSTAGRAM_ALREADY_CONNECTED_ERROR) )->important();
						return redirect($route)->send();
					}

					$this->socialRepository->createAndAttachToUser($socialUserObject, $provider, $currentUser);
					Flash::success($this->getProviderDisplayName($provider) . ' profile linked to your account')->important();
					return redirect($route);
				}else{
					Flash::error('Unable to add '.$this->getProviderDisplayName($provider).' profile to your account.')->important();
                    return redirect(route('profile.edit'))->send();
                }
            }else{
					if (isset($socialUserObject->id) && ($social = $this->socialRepository->getBySocialIdAndProvider($socialUserObject->id, $provider))!==null){
						$authorizingUser = $social->user;
						if ($authorizingUser && $authorizingUser->status=='active'){
							Auth::login($authorizingUser);

                            if ($authorizingUser->hasRole(User::ROLE_INSTRUCTOR)){
                                $route = session('prev_page', route('instructor.dashboard'));
                            }else{
                                if ( Cookie::has('backToRequestLesson') ){
                                    $route = route('profile', ['user' => Cookie::get('backToRequestLesson')]);
                                }else{
                                    $route = session('prev_page', route('student.dashboard'));
                                }
                            }
							return redirect($route);
						}else{
							Flash::error('User doesn\'t exist or not active')->important();
							return redirect(route('frontend.login'))->withErrors(['email'=>'User doesn\'t exist or not active'])->send();
						}
					}else{
						Flash::error('No such user. Register first to login and attach your '.$this->getProviderDisplayName($provider).' profile to your account on site.')->important();
						return redirect(route('frontend.login'))->send();
					}
            }
        }catch (InvalidStateException $e){
			Flash::error('Try again please')->important();
            return redirect(route('frontend.login'))->send();
        }
		Flash::error('Unable to process your request.')->important();
        return redirect(route('frontend.login'))->send();
    }

    /**
     * @param $provider
     * @param Request $request
     * @param InvitationRepository $invitationRepository
     * @return array|Application|RedirectResponse|Redirector
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getSocialInstructorRegistration($provider, Request $request, InvitationRepository $invitationRepository)
    {
		$redirectRoute = route('instructor.register');
		if (session()->has('invitation')){
			$invitation = session()->get('invitation');
			$invitation = ( is_array($invitation) && count($invitation)==1 ) ? $invitation[0] : $invitation;
			$redirectRoute .= '?invitation=' . $invitation;
			session()->forget('invitation');
		}

		if (!session()->has('submittedInstructor')){
			Flash::error('Bad Request')->important();
			return redirect($redirectRoute);
		}

		if ($request->get('denied') != '') {
			Flash::error('You did not share your profile data with our social app.')->important();
			return redirect($redirectRoute);
		}

		if (!in_array($provider, User::ALLOWED_SM_PROVIDERS)) {
			Flash::error('Not allowed social media provider.')->important();
			return redirect($redirectRoute);
		}

		$submittedInstructor = session()->get('submittedInstructor');
		$submittedInstructor = ( is_array($submittedInstructor) && count($submittedInstructor)==1 ) ? $submittedInstructor[0] : $submittedInstructor;

		$free_instructor_registration_enabled = Setting::getValue('free_instructor_registration_enabled', 0);
		$invitation = null;
		if ( isset($submittedInstructor['invitation'])
			 && $submittedInstructor['invitation']!=''
			 && ( $invitation = $invitationRepository->findUserInvitation($submittedInstructor['invitation']) ) == null
		) {
			Flash::error('Invitation does not exist')->important();
			return ['redirect' => route('home')];
		}
		if (!$free_instructor_registration_enabled){
			if ( ! isset($submittedInstructor['invitation'])
				 || strlen($submittedInstructor['invitation'])==0
				 || $invitation == null
			) {
				Flash::error('Invitation not defined')->important();
				return redirect(route('home'));
			}
		}

		try{
			$socialUserObject = Socialite::driver($provider)->user();
			if ($this->checkUniqueSocialmediaProfile($socialUserObject->id, $provider)){
				$providerName = $provider=='ig' ? 'Instagram' : ucfirst($provider);
				Flash::error( str_replace('{provider}', $providerName, self::INSTAGRAM_ALREADY_CONNECTED_ERROR) )->important();
				return redirect($redirectRoute)->send();
			}

			$irr = new InstructorRegisterRequest($submittedInstructor);
			$user = UserRegistrator::registerInstructor($irr);

			$this->socialRepository->createAndAttachToUser($socialUserObject, $provider, $user);

			switch ($provider){
				case 'ig':
					$user->profile->updateProfileWithInstagramData($socialUserObject);
					break;
				case 'twitter':
					break;
				case 'facebook':
					$user->profile->updateProfileWithFacebookData($socialUserObject);
					break;
				case 'snapchat':
					break;
			}

			session()->forget('submittedInstructor');

			$cookie = Cookie::make('instructorRegistered', 1, 5, '/', null, false, false);
			return redirect( route('home'))->withCookie($cookie)->send();
		}catch (InvalidStateException $e){
			Flash::error('Bad request:'.$e->getMessage())->important();
			return redirect($redirectRoute);
		}catch (\Exception $e){
			Flash::error('Error:'.$e->getMessage())->important();
			return redirect($redirectRoute);
		}

		Flash::error('Unable to process your request.')->important();
		return redirect(route('instructor.register'))->send();
	}

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function getSocialInstagramMedia(Request $request){
		$provider = $this->defaultProvider;

		$redirectRoute = route('instructor.gallery');

		if ($request->get('denied') != '') {
			Flash::error('You did not share your profile data with our social app.')->important();
			return redirect($redirectRoute);
		}

		$socialiteDriver = Socialite::driver($provider);
		$socialiteDriver->redirectUrl( route('social.instagram.media.update') );
		$socialUserObject = $socialiteDriver->user();

		Auth::user()->profile->updateProfileWithInstagramData($socialUserObject);
		Flash::info('Your recent Instagram media will be loaded in a few minutes')->important();
		return redirect($redirectRoute)->send();
	}

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getSocialInstagramStudentRegistration(Request $request) {
		$provider = $this->defaultProvider;
		$route = session()->has('current_page') ? session()->get('current_page') : route('home'); // route('student.register')
		if ($request->get('denied') != '') {
			Flash::error('You did not share your profile data with our social app.')->important();
			return redirect($route);
		}
		try{
			$clientId = config('services.instagram.client_id');
			$clientSecret = config('services.instagram.client_secret');
			$redirectUrl = route('social.instagram.student.registration');
			$config = new \SocialiteProviders\Manager\Config($clientId, $clientSecret, $redirectUrl);
			$socialUserObject = Socialite::driver($provider)->setConfig($config)->user();

			if ($this->checkUniqueSocialmediaProfile($socialUserObject->id, $provider)){
				Flash::error( str_replace('{provider}', 'Instagram', self::INSTAGRAM_ALREADY_CONNECTED_ERROR) )->important();
				return redirect($route)->send();
			}

			if (session()->has('submittedStudent')){
				$submittedStudent = session()->get('submittedStudent');
				$submittedStudent = ( is_array($submittedStudent) && count($submittedStudent)==1 ) ? $submittedStudent[0] : $submittedStudent;
			}else{
				$faker			= Faker\Factory::create('en_US');
				$fakeEmail		= $faker->regexify('[a-z0-9._%+-]{5,10}') . time() . getFakeEmailBase();
				$fakePassword	= $faker->regexify('[a-z0-9._%+-]{20,25}');
				$submittedStudent = [
					'first_name'	=> getFirstName($socialUserObject->name),
					'last_name'		=> getLastName($socialUserObject->name),
					'email'			=> $fakeEmail,
					'password'		=> $fakePassword,
					'password_confirmation' => $fakePassword
				];
			}

			$srr = new StudentRegisterRequest($submittedStudent);

			$user = UserRegistrator::registerStudent($srr);
			$this->socialRepository->createAndAttachToUser($socialUserObject, $provider, $user);
			// update profile data from instagram
			$user->profile->updateProfileWithInstagramData($socialUserObject);

			session()->forget('submittedStudent');

			Auth::login($user);

			return redirect(route('student.dashboard'));
		}catch (InvalidStateException $e){
			Flash::error('Bad request:'.$e->getMessage())->important();
			return redirect($route);
		}catch (\Exception $e){
			Flash::error('Error:'.$e->getMessage())->important();
			return redirect($route);
		}

		Flash::error('Unable to process your request.')->important();
		return redirect($route);
	}

    /**
     * @param $provider
     * @return Application|RedirectResponse|Redirector
     */
    public function detachSocial($provider)
    {
		if ($provider) {
			foreach (Auth::user()->social as $userSocial) {
				if ($userSocial->provider == $provider) {
					$userSocial->delete();
					Auth::user()->profile->update(['instagram_handle'=>null]);
					Flash::success($this->getProviderDisplayName($provider) . ' profile detached from your account')->important();
					return redirect(route('profile.edit'));
				}
			}
		}
		Flash::error('Unable to process your request.')->important();
		return redirect(route('profile.edit'))->send();
    }

    /**
     * @param $socialUserObjectId
     * @param $provider
     * @return mixed
     */
    private function checkUniqueSocialmediaProfile($socialUserObjectId, $provider){
		return $this->socialRepository->getBySocialIdAndProvider($socialUserObjectId, $provider);
	}

    /**
     * @param $provider
     * @return string
     */
    private function getProviderDisplayName($provider){
    	if ($provider=='ig')
			$provider = 'instagram';
		return ucfirst($provider);
	}
}
