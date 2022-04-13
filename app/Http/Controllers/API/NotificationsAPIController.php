<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\NotifyAPIRequest;
use App\Repositories\UserRepository;
use App\Http\Controllers\AppBaseController;
use App\Notifications\CustomUserNotification;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Class BookingAPIController
 * @package App\Http\Controllers\API
 */

class NotificationsAPIController extends AppBaseController
{
    /** @var  UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function notify(NotifyAPIRequest $request)
    {
		$usersToNotify = $this->userRepository->findWhereIn('id', $request->input('users'));

		if ($usersToNotify->count()==0)
			return $this->sendError('No users to notify', 400);

    	$message = $request->input('message');
//		if (!Auth::user()->hasRole(User::ROLE_STUDENT)) {
//			$use_methods = $request->input( 'notification_methods', [ 'email' ] );
//		}else{
			$use_methods = array_keys(Profile::getAvailableNotificationMethods());
//		}

		$sender = Auth::user();

		try{
			Notification::send($usersToNotify, new CustomUserNotification($message, $sender, $use_methods));
		}catch (\Exception $e){
			Log::error($e->getCode() . ': ' . $e->getMessage());
			return $this->sendError('Can\'t send notification: ' . $e->getMessage());
		}

        return $this->sendResponse(true, 'Notifications is being sent');
    }
}
