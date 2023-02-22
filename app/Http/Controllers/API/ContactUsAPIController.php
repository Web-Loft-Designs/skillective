<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\BecomeAnInstructorRequest;
use App\Http\Requests\API\ContactUsAPIRequest;
use App\Http\Controllers\AppBaseController;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\Admin\GuestBecomeInstructor;
use App\Notifications\ContactUsNotification;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;


class ContactUsAPIController extends AppBaseController
{

    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
    }


    /**
     * @param BecomeAnInstructorRequest $request
     * @return void
     */
    public function becomeInstructor(BecomeAnInstructorRequest $request)
    {
        $data = $request->validated();
        $user = User::create([
            'first_name' => $data['fullName'],
            'email' =>  $data['email'],
        ]);
        $administrators = $this->userRepository->getAdministrators();
        foreach ($administrators as $administrator)
        {
            $administrator->notify(new GuestBecomeInstructor($user->email));
        }
        $this->sendResponse('ok');
    }

    /**
     * @param ContactUsAPIRequest $request
     * @return JsonResponse
     */
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
