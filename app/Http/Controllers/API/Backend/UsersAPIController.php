<?php

namespace App\Http\Controllers\API\Backend;

use App\Models\User;
use App\Http\Requests\API\CancelUsersAPIRequest;
use App\Http\Requests\API\UpdateCountInvitesRequest;
use App\Repositories\BookingRepository;
use App\Repositories\LessonRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Prettus\Repository\Exceptions\RepositoryException;

class UsersAPIController extends AppBaseController
{
    /** @var  UserRepository */
    protected $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
        parent::__construct();
    }

    /**
     * @param CancelUsersAPIRequest $request
     * @param BookingRepository $bookingRepository
     * @param LessonRepository $lessonRepository
     * @return JsonResponse
     * @throws RepositoryException
     */
    public function suspendMany(CancelUsersAPIRequest $request, BookingRepository $bookingRepository, LessonRepository $lessonRepository)
	{
		$count_cancelled = 0;
		$totalToSuspend = count($request->input('users'));
		foreach ($request->input('users') as $userId){
			$user = $this->userRepository->findWithoutFail($userId);
			if ($user->status!=User::STATUS_BLOCKED) {
				if ($user->hasRole(User::ROLE_INSTRUCTOR)) {
					$lessonRepository->getInstructorUpcomingLessons($user->id)->each(function ($lesson) use ($user){
						$lesson->cancel();
					});
				}
				$user->suspend();
				$count_cancelled++;
			}else{
                if( $user->status=User::STATUS_BLOCKED )
                {
                    $this->deleteUser($request, $user);
                }
			}
		}
		$error = '';//$count_not_cancelled>0 ? 'Not suspended users have not completed bookings or upcoming lessons' : '';
		return $this->sendResponse(true, $count_cancelled . ' users from '.$totalToSuspend.' suspended.' . $error);
	}

    /**
     * @param $id
     * @param BookingRepository $bookingRepository
     * @param LessonRepository $lessonRepository
     * @return JsonResponse
     * @throws RepositoryException
     */
    public function suspend($id, BookingRepository $bookingRepository, LessonRepository $lessonRepository)
	{
		/** @var User $user */
		$user = $this->userRepository->findWithoutFail($id);

		if (empty($user)) {
			return $this->sendError('User not found');
		}

		if ($user->status!=User::STATUS_BLOCKED){
			if ($user->hasRole(User::ROLE_INSTRUCTOR)){
				$lessonRepository->getInstructorUpcomingLessons($user->id)->each(function ($lesson) use ($user){
					$lesson->cancel();
				});
			}

			$user->suspend();

			return $this->sendResponse(true, 'User suspended');
		}
		return $this->sendError('Can\'t suspend the user.', 400); //  User has not completed bookings or upcoming lessons
	}

    /**
     * @param UpdateCountInvitesRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function updateCountInvitesAllowed(UpdateCountInvitesRequest $request, User $user)
	{
		$user->profile->max_allowed_instructor_invites = $request->filled('max_allowed_instructor_invites') ? $request->input('max_allowed_instructor_invites') : null;
		$user->profile->save();
		return $this->sendResponse(true, 'Maximum allowed Instructor invitations updated');
	}

    /**
     * @param Request $request
     * @param User $user
     * @return void
     */
    public function deleteUser(Request $request, User $user){
		$user->profile->forcedelete();
		$user->forcedelete();
		$user->bookings()->forceDelete();
		$user->lessons()->forceDelete();
	}
}
