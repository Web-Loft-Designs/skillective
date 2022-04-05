<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;
use Auth;
use Log;
use App\Repositories\BookingRepository;

class InstructorPayoutsAPIController extends AppBaseController
{
    public function index(Request $request, BookingRepository $booking_repository)
    {
		$booking_repository->setPresenter("App\\Presenters\\BookingPayoutPresenter");
		try{
			$payouts = $booking_repository->presentResponse($booking_repository->getInstructorPayouts(Auth::user()->id, $request));
		}catch (\Exception $e){
			Log::error('getInstructorPayouts : ' . $e->getMessage());
			$payouts = ['data'=>[]];
		}

		return $this->sendResponse($payouts);
    }
}
