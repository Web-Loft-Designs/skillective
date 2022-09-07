<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\InviteStudentAPIRequest;
use App\Http\Requests\API\InviteInstructorAPIRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\ProfileRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\Invitation;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Notifications\InstructorRegistrationInvitation;
use App\Notifications\StudentRegistrationInvitation;
use App\Notifications\Admin\InviteNewInstructorRequest;
use GuzzleHttp\Client as HttpClient;
use Twilio\Rest\Client;

/**
 * Class BookingAPIController
 * @package App\Http\Controllers\API
 */

class InvitationAPIController extends AppBaseController
{

    /** @var  UserRepository */
    private $userRepository;

	/** @var  ProfileRepository */
	private $profileRepository;

    private $emailRegexp;
    private $phoneRegexp;

    public function __construct(UserRepository $userRepository, ProfileRepository $profileRepository)
    {
        $this->userRepository = $userRepository;
        $this->profileRepository = $profileRepository;
		$this->emailRegexp = getInviteEmailValidationRegexp();
		$this->phoneRegexp = getInviteMobilePhoneValidationRegexp();
    }

    public function inviteInstructor(InviteInstructorAPIRequest $request)
    {
		$input = $this->_prepareInputData($request);
        $invitedEmail = data_get($input, 'invited_email');
        $invitedMobilePhone = data_get($input, 'invited_mobile_phone');
        $invitedInstagramHandle = data_get($input, 'invited_instagram_handle');

		if (!$invitedEmail && !$invitedMobilePhone && !$invitedInstagramHandle) {
			return $this->sendError('Can\'t invite user. Invalid contact', 400);
		}

        if (Auth::user()->instructorInvitations()->count() >= Auth::user()->getMaxAllowedInstructorInvites()) {
			return $this->sendError('Can\'t invite user. Limit of invitations has been reached', 400);
		}

        if ($invitedEmail && $resultCheckEmailInv = $this->userRepository->checkInvitationFromEmail($invitedEmail)) {
            return $this->sendError($resultCheckEmailInv, 400);
        }

        if ($invitedMobilePhone && $resultCheckPhoneInv = $this->userRepository->checkInvitationFromPhone($invitedMobilePhone)){
            return $this->sendError($resultCheckPhoneInv, 400);
        }

        if ($invitedInstagramHandle && $resultCheckInstInv = $this->userRepository->checkInvitationFromInstagram($invitedInstagramHandle)){
            return $this->sendError($resultCheckInstInv, 400);
        }

		if (isset($input['invited_mobile_phone']) && $input['invited_mobile_phone']=='+12222222222') {
            $input['invited_mobile_phone'] = '+375298859083';
        }

		$successMessage = 'Invitation has been sent';

		DB::beginTransaction();
		try{
			$input['invited_as_instructor']	= true;
			$invitation = new Invitation($input);
			$invitation->save();

			if (Auth::user()->hasRole(User::ROLE_INSTRUCTOR)){
				$use_methods = $invitation->invited_mobile_phone!=null ? ['sms'] : ['email'];
				Notification::send($invitation, new InstructorRegistrationInvitation($invitation, $use_methods));
				DB::commit();
			} elseif (Auth::user()->hasRole(User::ROLE_STUDENT)){
				$successMessage = 'A request to invite instructor has been sent';
				// notify admins if registration complete and confirmation email sent
				$administrators = $this->userRepository->getAdministrators();
				foreach ($administrators as $administrator) {
					$administrator->notify(new InviteNewInstructorRequest($invitation));
				}
			}

			DB::commit();
		}catch (\Exception $e){
			DB::rollback();
			Log::error($e->getCode() . ': ' . $e->getMessage());
			return $this->sendError('Message hasn\'t been sent', 400 );
		}

        $count = intval(Auth::user()->getMaxAllowedInstructorInvites() - Auth::user()->instructorInvitations()->count());
        return $this->sendResponse($count, $successMessage);
    }

	public function inviteStudent(InviteStudentAPIRequest $request)
	{
		$input = $this->_prepareInputData($request);

		if (!isset($input['invited_email']) && !isset($input['invited_mobile_phone'])){
			return $this->sendError('Can\'t invite user. Invalid contact', 400);
		}
		if (Auth::user()->studentInvitations()->count()==Setting::getValue('max_allowed_student_invites')){
			return $this->sendError('Can\'t invite user. Limit of invitations has been reached', 400);
		}
		if (isset($input['invited_email']) && $this->userRepository->findByField('email', $input['invited_email'])->count()>0){
			return $this->sendError('Seems this user already has an account on our site', 400);
		}
		if (isset($input['invited_mobile_phone']) && $this->profileRepository->findByField('mobile_phone', $input['invited_mobile_phone'])->count()>0){
			return $this->sendError('Seems this user already has an account on our site', 400);
		}

		if (isset($input['invited_mobile_phone']) && $input['invited_mobile_phone']=='+12222222222')
			$input['invited_mobile_phone'] = '+375298859083';

		DB::beginTransaction();
		try{
			$input['invited_as_instructor']	= false;
			$invitation = new Invitation($input);
			$invitation->save();

			$use_methods = $invitation->invited_mobile_phone!=null ? ['sms'] : ['email'];

			Notification::send($invitation, new StudentRegistrationInvitation($invitation, $use_methods));
			DB::commit();
		}catch (\Exception $e){
			DB::rollback();
			Log::error($e->getCode() . ': ' . $e->getMessage());
			return $this->sendError('Message hasn\'t been sent', 400 );
		}

		return $this->sendResponse(true, 'Invitation has been sent');
	}

	private function _prepareInputData(Request $request){
		$input = [
			'invited_by'			=> Auth::user()->id,
			'invited_name'			=> $request->input('invited_name', '')
		];
		if (preg_match("/^{$this->emailRegexp}$/ix", $request->input('invited_contact'))){
			$input['invited_email'] = $request->input('invited_contact');
		}elseif (preg_match("/^{$this->phoneRegexp}$/ix", $request->input('invited_contact'))){
			$input['invited_mobile_phone'] = $request->input('invited_contact');
		}else{
			$igAccount = trim($request->input('invited_contact'), '@');
			$client = new HttpClient();
			$url = 'https://www.instagram.com/' . $igAccount;
			try{
				$r = $client->head($url);
				if ($r->getStatusCode() == 200)
					$input['invited_instagram_handle'] = $igAccount;
			}catch (\Exception $e){
				Log::error('invalid instagram account ' . $request->input('invited_contact'));
			}
		}
		return $input;
	}
}
