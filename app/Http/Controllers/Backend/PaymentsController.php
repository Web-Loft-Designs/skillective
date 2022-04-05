<?php

namespace App\Http\Controllers\Backend;

use App\Repositories\BookingRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Log;

class PaymentsController extends AppBaseController
{
	public function index(BookingRepository $bookingRepository, Request $request)
	{
		try{
			$bookingRepository->setPresenter("App\\Presenters\\BookingsPaymentsListPresenter");
			$bookings = $bookingRepository->presentResponse($bookingRepository->getBookings($request));
		}catch (\Exception $e){
			Log::error('getBookings : ' . $e->getMessage());
			$bookings = ['data'=>[]];
		}
		
		$vars = [
			'page_title' => 'Payments',
			'bookings' => $bookings
		];

		return view('backend.payments.index', $vars);
	}
}
