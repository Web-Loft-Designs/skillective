<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Auth;
use DB;
use Illuminate\Http\Request;
use Log;
use Cookie;
use App\Facades\IncomesCalculator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $currentPage = null;

	public function __construct()
	{
		// site settings to view
		$settings = app()->make('App\Models\Setting')->getAllAssociative();
		view()->share( 'settings', $settings );

		if (!app()->runningInConsole()){
			$this->currentPage = getCurrentPage();
			view()->share( 'currentPage', $this->currentPage );
			view()->share( 'currentUserCanBook', currentUserCanBook() );
		}

		$request = Request::capture();
		if (!Auth::user()){
//			view()->share( 'registration_via_instagram_url', Socialite::driver('ig')->with(['redirect_uri' => route('social.instagram.student.registration')])->redirect()->getTargetUrl() );

			$currentPath = '/' . ltrim($request->path() , '/');
			if(!$request->is('api/*') && strpos( substr($currentPath, strrpos($currentPath, '/') + 1), '.' )===false){
				if ( session()->has('current_page') ) { // redirect after login
					session()->put( 'prev_page', session()->get( 'current_page' ) );
				}
				session()->put('current_page', $currentPath);
			}

            view()->share( 'loggedUserRole', null );
		}else{
			if ( session()->has('current_page') )
				session()->forget(['current_page', 'prev_page']);

			$loggedUserRole = null;
			if (Auth::user()->hasRole(User::ROLE_STUDENT))
				$loggedUserRole = User::ROLE_STUDENT;
			elseif (Auth::user()->hasRole(User::ROLE_INSTRUCTOR))
				$loggedUserRole = User::ROLE_INSTRUCTOR;
			elseif (Auth::user()->hasRole(User::ROLE_ADMIN))
				$loggedUserRole = User::ROLE_ADMIN;
			view()->share( 'loggedUserRole', $loggedUserRole );

			if(!$request->is('api/*')){
				if (Auth::user()->hasFakeEmail()){
					flash('Setup your profile details please')->warning();
				}
				// check if there is queued Job to load instagram images to show a notification for user if not done yet
				if ( $loggedUserRole == User::ROLE_INSTRUCTOR ){//(now()->timestamp - Auth::user()->created_at->timestamp) < (3600 * 24) ){
					// failed jobs removing by scheduled command
					$jobTable = 'jobs';
					$hasLoadInstagramMediaJob = DB::table($jobTable)
							 ->where('payload', 'like', '%LoadInstagramMediaJob%')
							 ->where('payload', 'like', '%user_id\\\";i:'.Auth::user()->id.';%')
							->first();
					if ($hasLoadInstagramMediaJob)
						view()->share( 'loadingInstagramProfileImagesInQueue', true );
				}
				if ($request->getMethod()=='GET' && isDashboardPage() && $loggedUserRole==User::ROLE_INSTRUCTOR){
					$bookingRepository = app()->make('App\Repositories\BookingRepository');
					$countInstructorPendingBookings = $bookingRepository->getCountInstructorPendingBookings();
					view()->share( 'countInstructorPendingBookings', $countInstructorPendingBookings );
				}
				if ($request->getMethod()=='GET' && $loggedUserRole==User::ROLE_INSTRUCTOR){
					$countInstructorInvitationsSent = Auth::user()->instructorInvitations()->count();
					view()->share( 'countInstructorInvitationsSent', $countInstructorInvitationsSent );

					$totalAmountInEscrow = IncomesCalculator::totalAmountInEscrow(Auth::user()->id);
					view()->share( 'totalAmountInEscrow', $totalAmountInEscrow );

					// $totalPayoutYTD = IncomesCalculator::totalPayoutYTD(Auth::user()->id);
					// view()->share( 'totalPayoutYTD', $totalPayoutYTD );
				}

				view()->share( 'availableNotificationMethods', Profile::getAvailableNotificationMethods() );
			}
		}

		if(!$request->is('api/*')){
			view()->share( 'usStates', getUSStates() );
		}

	}
}
