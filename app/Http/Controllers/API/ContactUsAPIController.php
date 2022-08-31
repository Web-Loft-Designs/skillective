<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\BecomeAnInstructorRequest;
use App\Models\User;
use App\Http\Requests\API\ContactUsAPIRequest;
use App\Http\Controllers\AppBaseController;
use App\Models\Setting;
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

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function becomeInstructor(BecomeAnInstructorRequest $request)
    {

        $email = $request->input('email');
        $name = $request->input('fullName');

        $user = User::create([
            'first_name' => $name,
            'email' => $email,
        ]);

        $administrators = $this->userRepository->getAdministrators();
        foreach ($administrators as $administrator)
        {
            $administrator->notify(new GuestBecomeInstructor($email));
        }

        //$user->notify(new GuestBecomeInstructor($email));

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
