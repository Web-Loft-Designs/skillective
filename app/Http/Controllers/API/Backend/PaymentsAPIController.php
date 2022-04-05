<?php

namespace App\Http\Controllers\API\Backend;

use App\Repositories\BookingRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;
use Auth;
use Log;
use App\Models\Booking;
use App\Exports\BookingsPaymentsExport;
use Maatwebsite\Excel\Facades\Excel;

class PaymentsAPIController extends AppBaseController
{
    /** @var  BookingRepository */
    private $bookingRepository;

    public function __construct(BookingRepository $bookingRepo)
    {
        $this->bookingRepository = $bookingRepo;
    }

    public function index(Request $request)
    {
		try{
			$this->bookingRepository->setPresenter("App\\Presenters\\BookingsPaymentsListPresenter");
			$bookings = $this->bookingRepository->presentResponse($this->bookingRepository->getBookings($request));
		}catch (\Exception $e){
			Log::error('getBookings : ' . $e->getMessage());
			$bookings = ['data'=>[]];
		}

        return $this->sendResponse($bookings);
    }

	public function export(Request $request)
	{
		return Excel::download(new BookingsPaymentsExport($this->bookingRepository, $request), 'payments-list.csv');
	}
}
