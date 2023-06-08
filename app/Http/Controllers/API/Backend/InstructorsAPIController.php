<?php

namespace App\Http\Controllers\API\Backend;

use App\Notifications\InstructorRegistrationRequestApproved; // must finish registration
use App\Models\User;
use App\Models\Invitation;
use App\Notifications\InstructorRegistrationRequestDenied;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Exceptions\RepositoryException;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Repositories\InvitationRepository;
use Illuminate\Support\Facades\Log;

class InstructorsAPIController extends UsersAPIController
{

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws RepositoryException
     */
    public function index(Request $request)
	{
		$this->userRepository->setPresenter("App\\Presenters\\InstructorsInListPresenter");
		$roleID = Role::findByName(User::ROLE_INSTRUCTOR)->id;
		$users = $this->userRepository->presentResponse($this->userRepository->getUsers($request, $roleID));
		return $this->sendResponse($users);
	}

    /**
     * @param User $user
     * @param InvitationRepository $invitationRepository
     * @return JsonResponse
     */
    public function approve(User $user, InvitationRepository $invitationRepository)
	{
		if ($user->status != User::STATUS_ON_REVIEW || !$user->hasRole(User::ROLE_INSTRUCTOR))
			return $this->sendError('User not on review', 400);

		$user->setFinishRegistrationToken(Str::random(60));
		$user->setStatus(User::STATUS_APPROVED);

		$invitationId = $user->accepted_invitation_id;
		$this->_addFavoriteClientsAndInstructorsFromInvitations($user, $invitationId, $invitationRepository);

		return $this->sendResponse(true, 'User approved.');
	}

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function toggleFeatured(User $user)
	{
		if (!$user->isFeatured && User::ROLE_INSTRUCTOR) {
			DB::table('featured_instructors')->insert([
				'instructor_id' => $user->id
			]);
		} else {
			DB::table('featured_instructors')->where(
				'instructor_id',
				"=",
				$user->id
			)->delete();
		}

		return $this->sendResponse(true);
	}

    /**
     * @param User $user
     * @param Request $request
     * @return JsonResponse
     */
    public function setPriority(User $user, Request $request)
	{
		if (User::ROLE_INSTRUCTOR) {
			DB::table('featured_instructors')
				->where('instructor_id', $user->id)
				->update(['priority' => $request->priority]);
		}

		return $this->sendResponse(true);
	}

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function deny(User $user)
	{
		if ($user->status != User::STATUS_ON_REVIEW || !$user->hasRole(User::ROLE_INSTRUCTOR))
			return $this->sendError('User not on review', 400);

		$user->notify(new InstructorRegistrationRequestDenied($user));

		$user->profile->deleteOldImage();
		$user->media()->get()->each->delete();
		$this->userRepository->forceDelete($user->id);

		return $this->sendResponse(true, 'User application denied.');
	}

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function resendFinishRegistrationReminder(User $user)
	{
		if ($user->status != User::STATUS_APPROVED || !$user->hasRole(User::ROLE_INSTRUCTOR))
			return $this->sendError('Can\'t send reminder', 400);

		try {
			$user->notify(new InstructorRegistrationRequestApproved($user));
			return $this->sendResponse(true, 'Reminder has been sent.');
		} catch (\Exception $e) {
			Log::error("InstructorRegistrationRequestApproved Error for #{$user->id} : " . $e->getCode() . ': ' . $e->getMessage());
			return $this->sendError('Error while sending reminder:' . $e->getMessage(), 400);
		}
	}

    /**
     * @param User $user
     * @param $invitationId
     * @param InvitationRepository $invitationRepository
     * @return void
     */
    private function _addFavoriteClientsAndInstructorsFromInvitations(User $user, $invitationId, InvitationRepository $invitationRepository)
	{
		$userIsInstructor = $user->hasRole(User::ROLE_INSTRUCTOR);

		if ($invitationId) {
			$invitationUsedForRegistration = $invitationRepository->findWithoutFail($invitationId);

			$matchingInvitations = Invitation::select('invitations.*')
				->whereNull('invited_user_id')
				->with(['sender']);
			if ($invitationUsedForRegistration) {
				$conditions = "invited_instagram_handle='{$user->profile->instagram_handle}' OR invited_mobile_phone={$user->profile->mobile_phone} OR invited_email='{$user->email}'";
				if ($invitationUsedForRegistration->invited_mobile_phone)
					$conditions .= " OR invited_mobile_phone={$invitationUsedForRegistration->invited_mobile_phone} ";
				if ($invitationUsedForRegistration->invited_email)
					$conditions .= " OR invited_email='{$invitationUsedForRegistration->invited_email}' ";
				if ($invitationUsedForRegistration->invited_instagram_handle)
					$conditions .= " OR invited_instagram_handle='{$invitationUsedForRegistration->invited_instagram_handle}' ";
				$matchingInvitations = $matchingInvitations
					->where('id', '<>', $invitationId)
					->whereRaw("(  $conditions  )");
			} else {
				$matchingInvitations = $matchingInvitations->whereRaw("( invited_instagram_handle={$user->profile->instagram_handle} OR invited_mobile_phone={$user->profile->mobile_phone} OR invited_email='{$user->email}' )");
			}

			$matchingInvitations = $matchingInvitations->get();
			$matchingInvitations->push($invitationUsedForRegistration);
		} else {
			$matchingInvitations = Invitation::select('invitations.*')
				->whereNull('invited_user_id')
				->whereRaw("( invited_instagram_handle='{$user->profile->instagram_handle}' OR invited_mobile_phone={$user->profile->mobile_phone} OR invited_email='{$user->email}' )")
				->with(['sender'])
				->get();
		}

		foreach ($matchingInvitations as $invitation) {
			if ($userIsInstructor == false && $invitation->invited_as_instructor == false && $invitation->sender->hasRole(User::ROLE_INSTRUCTOR) && $invitation->sender->clients()->where('client_id', $user->id)->count() == 0) {
				$invitation->sender->clients()->sync($user);
			} elseif ($userIsInstructor == true && $invitation->invited_as_instructor == true  && !$invitation->sender->hasRole(User::ROLE_INSTRUCTOR) && $invitation->sender->instructors()->where('instructor_id', $user->id)->count() == 0) {
				$invitation->sender->instructors()->sync($user);
			}
		}

		$matchingInvitations->each(function ($invitation) use ($user) {
			$invitation->update(['invited_user_id' => $user->id, 'invitation_token' => '']);
		});
	}
}
