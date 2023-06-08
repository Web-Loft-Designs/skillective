<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Repositories\BookingRepository;
use Illuminate\Support\Facades\Log;

class InstructorPayoutsAPIController extends AppBaseController
{
    /**
     * @param Request $request
     * @param BookingRepository $booking_repository
     * @return JsonResponse
     */
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
