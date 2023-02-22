<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\NotifyAPIRequest;
use App\Repositories\UserRepository;
use App\Http\Controllers\AppBaseController;
use App\Notifications\CustomUserNotification;
use App\Models\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NotificationsAPIController extends AppBaseController
{
    /** @var  UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
    }

    /**
     * @param NotifyAPIRequest $request
     * @return JsonResponse
     */
    public function notify(NotifyAPIRequest $request)
    {
        // TODO Баг коли notif  відправляє інструктор все норм якщо студент то ошибка звязана з невірним
        //  форматом масива  $request->input('users') потрібно щоб фронт пофіксили.
		$usersToNotify = $this->userRepository->findWhereIn('id', $request->input('users'));

		if ($usersToNotify->count()==0) {
            return $this->sendError('No users to notify', 400);
        }

    	$message = $request->input('message');
		$use_methods = array_keys(Profile::getAvailableNotificationMethods());
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
