<?php

namespace App\Observers;

use App\Models\User;
use App\Notifications\InstructorRegistrationRequestApproved; // must finish registration
use App\Notifications\StudentMustFinishRegistration;
use App\Notifications\StudentRegistrationConfirmation;
use App\Notifications\InstructorRegistrationConfirmation;
use App\Notifications\StudentRegistrationAdminNotification;
use App\Notifications\InstructorRegistrationAdminNotification;
use App\Notifications\MerchantAccount\SubMerchantAccountApproved;
use App\Notifications\MerchantAccount\SubMerchantAccountDeclined;
use App\Notifications\InstructorRegistrationRequestSentToReview;
use App\Notifications\UserAccountSuspended;
use App\Repositories\UserRepository;
use Braintree\MerchantAccount;
use Illuminate\Support\Facades\Log;

class UserObserver
{
	/** @var  UserRepository */
	private $userRepository;

	public function __construct(UserRepository $userRepo) {
		$this->userRepository = $userRepo;
	}

	public function submerchantStatusChanged(User $user){
		switch ($user->bt_submerchant_status) {
			case MerchantAccount::STATUS_ACTIVE:
				try{
					$user->notify(new SubMerchantAccountApproved($user));
				}catch (\Exception $e){
					Log::error("SubMerchantAccountApproved Error for #{$user->id} : " . $e->getCode() . ': ' . $e->getMessage());
				}
				break;
			case MerchantAccount::STATUS_SUSPENDED:
				try{
					$user->notify(new SubMerchantAccountDeclined($user));
				}catch (\Exception $e){
					Log::error("SubMerchantAccountDeclined Error for #{$user->id} : " . $e->getCode() . ': ' . $e->getMessage());
				}
				break;
		}
	}

	public function statusChanged(User $user)
    {
        switch ($user->status){
			case User::STATUS_ON_REVIEW:
				if ($user->hasRole(User::ROLE_INSTRUCTOR)) {
					try {
						$user->notify( new InstructorRegistrationRequestSentToReview( $user ) );
					} catch ( \Exception $e ) {
						Log::error( 'can\'t send InstructorRegistrationRequestSentToReview' );
					}
					// notify admins if registration complete and confirmation email sent
					$administrators = $this->userRepository->getAdministrators();
					foreach ($administrators as $administrator) {
						try{
							$administrator->notify(new InstructorRegistrationAdminNotification($user));
						}catch (\Exception $e){
							Log::error("InstructorRegistrationAdminNotification Error for #{$administrator->id} : " . $e->getCode() . ': ' . $e->getMessage());
						}
					}
				}
				break;
			case User::STATUS_APPROVED:
				if ($user->hasRole(User::ROLE_INSTRUCTOR)){
					try{
						$user->notify(new InstructorRegistrationRequestApproved($user));
					}catch (\Exception $e){
						Log::error("InstructorRegistrationRequestApproved Error for #{$user->id} : " . $e->getCode() . ': ' . $e->getMessage());
					}
				} elseif ($user->hasRole(User::ROLE_STUDENT)){
					try{
						$user->notify(new StudentMustFinishRegistration($user));
					}catch (\Exception $e){
						Log::error("StudentMustFinishRegistration Error for #{$user->id} : " . $e->getCode() . ': ' . $e->getMessage());
					}
				}
				break;
			case User::STATUS_BLOCKED:
				try{
					$user->notify(new UserAccountSuspended($user));
				}catch (\Exception $e){
					Log::error("UserAccountSuspended Error for #{$user->id} : " . $e->getCode() . ': ' . $e->getMessage());
				}
				break;
			case User::STATUS_ACTIVE:
				if ($user->hasRole(User::ROLE_INSTRUCTOR)){
					try{
						$user->notify(new InstructorRegistrationConfirmation($user));
					}catch (\Exception $e){
						Log::error("InstructorRegistrationConfirmation Error for #{$user->id} : " . $e->getCode() . ': ' . $e->getMessage());
					}
				}elseif ($user->hasRole(User::ROLE_STUDENT)){
					// send notification to real emails (fake email address can be generated when 1click registration via instagram)
					if (!$user->hasFakeEmail()){
						try{
							$user->notify(new StudentRegistrationConfirmation($user));
						}catch (\Exception $e){
							Log::error("StudentRegistrationConfirmation Error for #{$user->id} : " . $e->getCode() . ': ' . $e->getMessage());
						}
					}
					// notify admins if registration complete and confirmation email sent
					$administrators = $this->userRepository->getAdministrators();
					foreach ($administrators as $administrator) {
						try{
							$administrator->notify(new StudentRegistrationAdminNotification($user));
						}catch (\Exception $e){
							Log::error("StudentRegistrationAdminNotification Error for #{$administrator->id} : " . $e->getCode() . ': ' . $e->getMessage());
						}
					}
				}
				break;
        }
    }
}
