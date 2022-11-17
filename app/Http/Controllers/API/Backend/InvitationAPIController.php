<?php

namespace App\Http\Controllers\API\Backend;

use App\Repositories\InvitationRepository;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\ProfileRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\Invitation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\InstructorRegistrationInvitation;
use App\Notifications\StudentRegistrationInvitation;
use Illuminate\Support\Facades\Notification;


class InvitationAPIController extends AppBaseController
{

    /** @var  UserRepository */
    private $userRepository;

	/** @var  ProfileRepository */
	private $profileRepository;

    private $emailRegexp;
    private $phoneRegexp;
    private $invitationRepository;

    public function __construct(UserRepository $userRepository, ProfileRepository $profileRepository, InvitationRepository $invitationRepository)
    {
        $this->userRepository = $userRepository;
        $this->profileRepository = $profileRepository;
		$this->emailRegexp = getInviteEmailValidationRegexp();
		$this->phoneRegexp = getInviteMobilePhoneValidationRegexp();
        $this->invitationRepository = $invitationRepository;
    }

    public function inviteInstructors(Request $request)
    {
    	$resultMessage = '';
    	$countInvited = 0;
		$contacts_to_invite = $request->input('contacts_to_invite', '');

		if (!is_array($contacts_to_invite)) {
            $contacts_to_invite = explode(',', $contacts_to_invite);
        }

    	foreach ($contacts_to_invite as $contactToInvite){
			$contactToInvite = trim($contactToInvite);

            if ($contactToInvite === '') {
                continue;
            }

            $input = $this->_prepareInputData($contactToInvite);
            $invitedEmail = data_get($input, 'invited_email');
            $invitedMobilePhone = data_get($input, 'invited_mobile_phone');

            if (!$invitedEmail && !$invitedMobilePhone){
				$resultMessage .= ($contactToInvite . ': Can\'t invite user. Invalid contact<br>');
				continue;
			}

            if ($invitedEmail && $resultCheckEmailInv = $this->userRepository->checkInvitationFromEmail($invitedEmail)) {
                $resultMessage .= ($contactToInvite . ': ' . $resultCheckEmailInv . '<br>');
                continue;
            }

            if ($invitedMobilePhone && $resultCheckPhoneInv = $this->userRepository->checkInvitationFromPhone($invitedMobilePhone)){
                $resultMessage .= ($contactToInvite . ': ' . $resultCheckPhoneInv . '<br>');
                continue;
			}

			if (isset($input['invited_mobile_phone']) && $input['invited_mobile_phone'] === '+12222222222') {
                $input['invited_mobile_phone'] = '+375298859083';
            }

			DB::beginTransaction();
			try{
				$input['invited_as_instructor']	= true;
				$invitation = new Invitation($input);
				$invitation->save();

				$use_methods = $invitation->invited_mobile_phone != null ? ['sms'] : ['email'];

				Notification::send($invitation, new InstructorRegistrationInvitation($invitation, $use_methods));
				$countInvited++;
				DB::commit();
			}catch (\Exception $e){
				DB::rollback();
				Log::error($e->getCode() . ': ' . $e->getMessage());
				$resultMessage .= ($contactToInvite . ': '.$e->getMessage().'<br>');
			}
		}

		$resultMessage .= "{$countInvited} invitations sent.";

		return $this->sendResponse(true, $resultMessage);
    }

	public function inviteStudents(Request $request)
	{
		$resultMessage = '';
		$countInvited = 0;
		$contacts_to_invite = $request->input('contacts_to_invite', '');

		if (!is_array($contacts_to_invite)) $contacts_to_invite = explode(',', $contacts_to_invite);

		foreach ($contacts_to_invite as $contactToInvite){
			$contactToInvite = trim($contactToInvite);

			if ($contactToInvite=='') continue;

			$input = $this->_prepareInputData($contactToInvite);

			if (!isset($input['invited_email']) && !isset($input['invited_mobile_phone'])){
				$resultMessage .= ($contactToInvite . ': Can\'t invite user. Invalid contact<br>');
				continue;
			}

			if (isset($input['invited_email'])){
				if($this->userRepository->findByField('email', $input['invited_email'])->count() > 0 ||
                    Invitation::where('invited_email', $input['invited_email'])->count() > 0) {
                    $resultMessage .= ($contactToInvite . ': You have already invited this student.<br>');
                    continue;
                }
			}

			if (isset($input['invited_mobile_phone'])){
                if($this->profileRepository->findByField('mobile_phone', $input['invited_mobile_phone'])->count() > 0 ||
                    Invitation::where('invited_mobile_phone', $input['invited_mobile_phone'])->count() > 0) {
                    $resultMessage .= ($contactToInvite . ': You have already invited this student<br>');
                    continue;
                }
			}

			DB::beginTransaction();
			try{
				$input['invited_as_instructor']	= false;
				$invitation = new Invitation($input);
				$invitation->save();

				$use_methods = $invitation->invited_mobile_phone!=null ? ['sms'] : ['email'];

				Notification::send($invitation, new StudentRegistrationInvitation($invitation, $use_methods));
				$countInvited++;
				DB::commit();
			}catch (\Exception $e){
				DB::rollback();
				Log::error($e->getCode() . ': ' . $e->getMessage());
				$resultMessage .= ($contactToInvite . ': '.$e->getMessage().'<br>');
			}
		}

		$resultMessage .= "{$countInvited} invitations sent.";

		return $this->sendResponse(true, $resultMessage);
	}

	private function _prepareInputData($contact){
		$input = [
			'invited_by'			=> auth()->id(),
			'invited_name'			=> ''
		];

		if (preg_match("/^{$this->emailRegexp}$/ix", $contact)){
			$input['invited_email'] = $contact;
		} elseif (preg_match("/^{$this->phoneRegexp}$/ix", $contact)) {
			$input['invited_mobile_phone'] = $contact;
		}

		return $input;
	}

    public function inviteResendInstructors(Request $request)
    {
        $resultMessage = '';
        $countInvited = 0;
        $contacts_to_invite = $request->input('contacts_to_invite', '');


        if (!is_array($contacts_to_invite)) {
            $contacts_to_invite = explode(',', $contacts_to_invite);
        }

        foreach ($contacts_to_invite as $contactToInvite){
            $contactToInvite = trim($contactToInvite);

            if ($contactToInvite === '') {
                continue;
            }

            $input = $this->_prepareInputData($contactToInvite);
            $invitedEmail = data_get($input, 'invited_email');
            $invitedMobilePhone = data_get($input, 'invited_mobile_phone');

            if (!$invitedEmail && !$invitedMobilePhone){
                $resultMessage .= ($contactToInvite . ': Can\'t invite user. Invalid contact<br>');
                continue;
            }


            if ($invitedEmail && $resultCheckEmailInv = $this->userRepository->checkResendInvitationFromEmail($invitedEmail)) {
                $resultMessage .= ($contactToInvite . ': ' . $resultCheckEmailInv . '<br>');
                continue;
            }

            if ($invitedMobilePhone && $resultCheckPhoneInv = $this->userRepository->checkResendInvitationFromPhone($invitedMobilePhone)){
                $resultMessage .= ($contactToInvite . ': ' . $resultCheckPhoneInv . '<br>');
                continue;
            }

            if (isset($input['invited_mobile_phone']) && $input['invited_mobile_phone'] === '+12222222222') {
                $input['invited_mobile_phone'] = '+375298859083';
            }

            DB::beginTransaction();
            try{
                $input['invited_as_instructor']	= true;
                $invitation = new Invitation($input);
                $invitation->save();

                $use_methods = $invitation->invited_mobile_phone != null ? ['sms'] : ['email'];

                Notification::send($invitation, new InstructorRegistrationInvitation($invitation, $use_methods));
                $countInvited++;
                DB::commit();
            }catch (\Exception $e){
                DB::rollback();
                Log::error($e->getCode() . ': ' . $e->getMessage());
                $resultMessage .= ($contactToInvite . ': '.$e->getMessage().'<br>');
            }
        }

        $resultMessage .= "{$countInvited} invitations sent.";

        return $this->sendResponse(true, $resultMessage);
    }

    public  function getInvitations(Request $request)
    {
        $invitations = $this->invitationRepository
            ->presentResponse($this->invitationRepository->getInvitations($request));

        return $this->sendResponse($invitations, 'Invitations');
    }
}
