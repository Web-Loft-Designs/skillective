<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\BookingUserDataAPIRequest;
use App\Http\Requests\API\CreateBookingAPIRequest;
use App\Models\Lesson;
use App\Models\User;
use App\Repositories\BookingRepository;
use App\Repositories\UserRepository;
use Braintree\MerchantAccount;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;


class BookingAPIController extends AppBaseController
{
    /** @var  BookingRepository */
    private $bookingRepository;

    public function __construct(BookingRepository $bookingRepo)
    {
        parent::__construct();
        $this->bookingRepository = $bookingRepo;
    }


    /**
     * @param BookingUserDataAPIRequest $request
     * @param Lesson $lesson
     * @return JsonResponse
     */
    public function validateUserData(BookingUserDataAPIRequest $request, Lesson $lesson){ // call before showing payment form
		if ( ($error = $this->_checkForLessonAvailability($lesson))!='' ){
			return $this->sendError($error, 400);
		}

		$settingsModel = App::make('App\Models\Setting');
		$settingsModel->incrementValue('report_count_payment_form_views');

		return $this->sendResponse(true, 'User data valid');
	}

    /**
     * @param $lesson
     * @return string
     */
    private function _checkForLessonAvailability($lesson){
		if ( $lesson->getCountFreeSpots()==0 ){
			return 'No free spots left';
		}
        if ($lesson->private_for_student_id && $lesson->private_for_student_id!=Auth::id()){
            return 'You can\'t book this Private Lesson';
        }
		if ( $lesson->is_cancelled ){
			return 'Lesson cancelled';
		}
		if ($lesson->alreadyStarted()){
			return 'Lesson already started. Booking disabled.';
		}
		if (!currentUserCanBook()){
			return 'You have no permissions to book lessons';
		}
		// any other reasons with same error message
		if (!$lesson->isBookableNowByCurrentUser()){
			return 'Lesson can\'t be booked';
		}
		if (config('app.env')=='prod'
			&& (
				$lesson->instructor->bt_submerchant_id==null
				|| $lesson->instructor->bt_submerchant_status!=MerchantAccount::STATUS_ACTIVE
				|| $lesson->instructor->status != User::STATUS_ACTIVE
			)
		){
				return 'Instructor not active or doesn\'t have a merchant account';
		}
		return '';
	}

    /**
     * @param CreateBookingAPIRequest $request
     * @param Lesson $lesson
     * @param UserRepository $user_repository
     * @return JsonResponse
     */
    public function store(CreateBookingAPIRequest $request, Lesson $lesson, UserRepository $user_repository)
    {
		if ( ($error = $this->_checkForLessonAvailability($lesson))!='' ){
			return $this->sendError($error, 400);
		}
		try{
			$booking = $lesson->book($request, $user_repository);
			return $this->sendResponse($this->bookingRepository->presentResponse($booking)['data'], 'Booking created');
		}catch (\Exception $e){
			return $this->sendError('Unable to book lesson: ' . $e->getMessage(), 400);
		}
    }
}
