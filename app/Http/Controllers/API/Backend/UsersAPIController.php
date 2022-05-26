<?php

namespace App\Http\Controllers\API\Backend;

use App\Models\User;
use App\Http\Requests\API\CancelUsersAPIRequest;
use App\Http\Requests\API\UpdateCountInvitesRequest;
use App\Repositories\BookingRepository;
use App\Repositories\LessonRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;
use Auth;

class UsersAPIController extends AppBaseController
{
    /** @var  UserRepository */
    protected $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
    }

	public function suspendMany(CancelUsersAPIRequest $request, BookingRepository $bookingRepository, LessonRepository $lessonRepository)
	{
		$count_cancelled = 0;
		$count_not_cancelled = 0;

		$requestStudentBookingsRequest = new Request([
			'limit'   => 1,
			'type'	=> 'active'
		]);
		$requestInstructorBookingsRequest = new Request([
			'limit'   => 1,
			'type'	=> 'active'
		]);
		$totalToSuspend = count($request->input('users'));
		foreach ($request->input('users') as $userId){

			$user = $this->userRepository->findWithoutFail($userId);

			if (
				$user->status!=User::STATUS_BLOCKED
//				|| $bookingRepository->getStudentBookings($requestStudentBookingsRequest, $user->id)->count()>0
//				|| $bookingRepository->getInstructorBookings($requestInstructorBookingsRequest, $user->id)->count()>0
//				|| $lessonRepository->getInstructorUpcomingLessons($user->id)->count()>0
			){
				if ($user->hasRole(User::ROLE_INSTRUCTOR)){
					$lessonRepository->getInstructorUpcomingLessons($user->id)->each(function ($lesson) use ($user){
						$lesson->cancel();
					});
				}

				$user->suspend();

				$count_cancelled++;
			}else{
				$count_not_cancelled++;

                if( $user->status=User::STATUS_BLOCKED )
                {
                    $this->deleteUser($request, $user);
                }

			}
		}
		$error = '';//$count_not_cancelled>0 ? 'Not suspended users have not completed bookings or upcoming lessons' : '';
		return $this->sendResponse(true, $count_cancelled . ' users from '.$totalToSuspend.' suspended.' . $error);
	}

	public function suspend($id, BookingRepository $bookingRepository, LessonRepository $lessonRepository)
	{
		/** @var User $user */
		$user = $this->userRepository->findWithoutFail($id);

		if (empty($user)) {
			return $this->sendError('User not found');
		}

		$requestStudentBookingsRequest = new Request([
			'limit'   => 1,
			'type'	=> 'active'
		]);
		$requestInstructorBookingsRequest = new Request([
			'limit'   => 1,
			'type'	=> 'active'
		]);

		if (
			$user->status!=User::STATUS_BLOCKED
//			|| $bookingRepository->getStudentBookings($requestStudentBookingsRequest, $user->id)->count()>0
//			|| $bookingRepository->getInstructorBookings($requestInstructorBookingsRequest, $user->id)->count()>0
//			|| $lessonRepository->getInstructorUpcomingLessons($user->id)->count()>0
		){
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

	public function updateCountInvitesAllowed(UpdateCountInvitesRequest $request, User $user)
	{
		$user->profile->max_allowed_instructor_invites = $request->filled('max_allowed_instructor_invites') ? $request->input('max_allowed_instructor_invites') : null;
		$user->profile->save();
		return $this->sendResponse(true, 'Maximum allowed Instructor invitations updated');
	}

	public function deleteUser(Request $request, User $user){
		$user->profile->forcedelete();
		$user->forcedelete();
		$user->bookings()->forceDelete();
		$user->lessons()->forceDelete();
	}
}
