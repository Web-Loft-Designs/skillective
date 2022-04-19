<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
use App\Models\Genre;
use App\Models\Setting;
use App\Repositories\GenreRepository;
use App\Repositories\UserRepository;
use App\Repositories\LessonRepository;
use App\Models\UserGeoLocation;
use Session;
use Auth;
use Spatie\Permission\Models\Role;
use App\Facades\BraintreeProcessor;
use Log;

class ProfileController extends Controller
{
	/** @var  GenreRepository */
	private $genreRepository;

	/** @var  UserRepository */
	private $userRepository;

	/** @var  LessonRepository */
	private $lessonRepository;

	public function __construct(GenreRepository $genreRepo, UserRepository $userRepo, LessonRepository $lessonRepo)
	{
		$this->genreRepository	= $genreRepo;
		$this->userRepository	= $userRepo;
		$this->lessonRepository	= $lessonRepo;

		parent::__construct();
	}

    /**
     * @param Request $request
     * @param User $user
     * @return \Illuminate\View\View
     */
    public function show(User $user)
    {
		$currentUserIsAdmin = (Auth::user() && Auth::user()->hasRole(User::ROLE_ADMIN));
    	if ($user && !$user->id && !$currentUserIsAdmin)
			$user = Auth::user();
    	if (!$user){
			return redirect(route('frontend.login'));
		}
		if ($user->status!=User::STATUS_ACTIVE && !$currentUserIsAdmin)
			return abort(404);
		if ($user->hasRole(User::ROLE_ADMIN))
			return abort(404);

		$userData = $this->userRepository->getUserData($user->id);
		$userData = $this->userRepository->presentResponse($userData)['data'];

		$invitedInstructors = [];
//		$userData['genres'] = $user->genres()->pluck('id')->toArray();
		$isInstructor = false;
		if ($user->hasRole(User::ROLE_INSTRUCTOR)) {
			$isInstructor = true;

			$userData['total_count_lessons'] = $this->lessonRepository->getInstructorsPastBookedLessonsCount($user->id);//$this->lessonRepository->getInstructorUpcomingLessons($user->id)->count();
			$userData['lessons_rate_min']    = (float)$user->myLessonsBookings()
                ->whereRaw("( bookings.status <> '".Booking::STATUS_CANCELLED."') AND bookings.created_at>'".date('Y-m-d H:i:s', strtotime('-6 months'))."'")
                ->min( 'spot_price' );
			$userData['lessons_rate_max']    = (float)$user->myLessonsBookings()
                ->whereRaw("( bookings.status <> '".Booking::STATUS_CANCELLED."') AND bookings.created_at>'".date('Y-m-d H:i:s', strtotime('-6 months'))."'")
                ->max( 'spot_price' );
			$userData['upcoming_locations']  = $this->lessonRepository->getUpcomingInstructorLocations($user->id);
			$userData['upcoming_virtual_lessons_dates']  = $this->lessonRepository->getUpcomingInstructorVirtualLessonsDates($user->id);

			if ($currentUserIsAdmin){
				$invitedByRequest = new Request([
					'limit'	=> 999,
					'invited_by' => $user->id,
					'status'	=> 'all'
				]);

				$instructorRoleId = Role::findByName(User::ROLE_INSTRUCTOR)->id;
				$this->userRepository->setPresenter("App\\Presenters\\InstructorsInListPresenter");
				$invitedInstructors = $this->userRepository->presentResponse($this->userRepository->getUsers($invitedByRequest, $instructorRoleId))['data']; // TODO: do we really need this data here?
			}

		}else{
			$userData['total_booked_lessons'] = $user->bookings()->where('status', '<>', Booking::STATUS_CANCELLED)->count();
		}

		$userData['isInstructor']		 = $isInstructor;
		$userData['isStudent']			 = !$isInstructor;

		$template = $isInstructor ? 'instructor' : 'student';

		$vars = [
			'page_title'		=> ($isInstructor ? 'Instructor': 'Client') . " Profile",
			'userProfileData'	=> $userData,
			'siteGenres' => $this->genreRepository->presentResponse($this->genreRepository->getSiteGenres())['data'],
			'userMedia'	=> $user->getGalleryMedia(),
			'invitedInstructors' => $invitedInstructors,
            'authUserIsAdmin' => (Auth::user() && Auth::user()->hasRole('Admin')),
            'dashboardPage' => getCurrentPage('instructor/dashboard'),
            'booking_fees_description' => getCurrentPageMetaValue($dashboardPage, 'booking_fees_description'),
		];

		if(Auth::user()){
			$vars['userGengres'] = $this->genreRepository->presentResponse(Auth::user() ? Auth::user()->genres : [])['data'];
		}

		return view("frontend.{$template}.profile", $vars);
    }

	public function edit(Request $request, User $user = null)
	{
		$isAdmin = false;
		if (!Auth::user()->hasRole(User::ROLE_ADMIN)){
			$user = Auth::user();
		}else{
			$isAdmin = true;
		}
		if (!$user)
			return abort(404);

		if (!$this->currentPage){
			$this->currentPage = getCurrentPage('profile/edit');
			view()->share( 'currentPage', $this->currentPage );
		}

		$userData = $this->userRepository->getUserData($user->id);
		$userData = $this->userRepository->presentResponse($userData)['data'];

		$isInstructor = false;
		if ($user->hasRole(User::ROLE_INSTRUCTOR)){
			$isInstructor = true;
		}

		$userData['genres'] = $user->genres()->pluck('id')->toArray();
		$userData['noPassword'] = (strpos($user->password, 'skillectivefake-')===0);
		$userData['isInstructor']		 = $isInstructor;
		$userData['isStudent']			 = !$isInstructor;
		$template = $isInstructor ? 'instructor' : 'student';

		$vars = [
			'page_title'		=> "User Profile",
			'userProfileData'	=> $userData,
			'userGeoLocations'	=> $user->geoLocations()->get()->toArray(),
			'availableLimits' => UserGeoLocation::getAvailableLimits(),
			'categorizedGenres' => $this->genreRepository->getCategorizedGenres(),
			'userMedia'	=> $user->getGalleryMedia(),
			'siteGenres'	=> $this->genreRepository->presentResponse($this->genreRepository->getSiteGenres())['data'],
            'userGenres'	=> $this->genreRepository->presentResponse(Auth::user()->genres)['data']
		];

		if ($isInstructor){
			$savedMerchantAccountDetails = BraintreeProcessor::getMerchantAccountDetails($user);
			// useful for local site which doesn't receive webhook notifications
			if ($savedMerchantAccountDetails!=null && $savedMerchantAccountDetails['status']=='active' && $user->bt_submerchant_status=='pending'){
				$this->userRepository->updateUserSubMerchantStatus( $user->bt_submerchant_id, \Braintree_MerchantAccount::STATUS_ACTIVE );
			}
			$vars['savedMerchantAccountDetails']  = $savedMerchantAccountDetails;

		}

		if ($isAdmin){
			$vars['defaultMaxAllowedInstructorInvites']  = Setting::getValue('max_allowed_instructor_invites');
			$vars['countInstructorInvitationsSent']  = $user->instructorInvitations()->count();
			$vars['countInstructorInvitationsApplied']  = $user->instructorInvitations()->whereNotNull('invited_user_id')->count();
		}
//		dd(BraintreeProcessor::deleteAllCustomerPaymentMethods($user));
//		dd(BraintreeProcessor::getAllCustomerPaymentMethods($user));
//dd(BraintreeProcessor::getSavedCustomerPaymentMethods($user));

		if (!$isAdmin && !$isInstructor){
			$vars['clientToken'] = BraintreeProcessor::generateClientToken($user);
			$vars['paymentMethods'] = BraintreeProcessor::getSavedCustomerPaymentMethods($user);
			$vars['paymentEnvironment'] = config('services.braintree.environment');
		}
		return view("frontend.{$template}.profile-edit", $vars);
	}
}
