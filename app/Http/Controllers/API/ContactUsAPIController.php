<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Http\Requests\API\ContactUsAPIRequest;
use App\Http\Controllers\AppBaseController;
use Response;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use App\Notifications\ContactUsNotification;
use Notification;
use Log;
use Illuminate\Http\Request;
use App\Notifications\Admin\GuestBecomeInstructor;
use App\Repositories\UserRepository;

/**
 * Class LessonController
 * @package App\Http\Controllers\API
 */

class ContactUsAPIController extends AppBaseController
{

	/** @var  UserRepository */
	private $userRepository;

	public function __construct(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
	}

    public function becomeInstructor(Request $request)
    {
        $guest_email = $request->input('email');

        if (!$guest_email) {
            return $this->sendError('Please, enter valid email');
        }

        (new User)->forceFill([
            'name' => 'Their name',
            'email' => 'louis@skillective.com',
        ])->notify(new GuestBecomeInstructor($guest_email));

        $this->sendResponse('ok');
    }

	public function send(ContactUsAPIRequest $request)
	{
		$recepients = explode(',', Setting::getValue('contact_form_recepients'));
		if (count($recepients) > 0) {
			$formData = $request->all();
			foreach ($recepients as $recepient) {
				$result = filter_var($recepient, FILTER_VALIDATE_EMAIL);
				try {
					Notification::route('mail', $result)->notify(new ContactUsNotification($formData));
				} catch (\Exception $e) {
					Log::error("ContactUsNotification Error: " . $e->getCode() . ': ' . $e->getMessage());
					return $this->sendError('Message has not been sent!', 400);
				}
			}
			return $this->sendResponse(true, 'Message has been sent! We will contact you as soon as possible.');
		}

		return $this->sendError('Message has not been sent!', 400);
	}
}
