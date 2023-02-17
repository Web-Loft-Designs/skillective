<?php

namespace App\Http\Controllers\API\Backend;

use App\Repositories\BookingRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Exports\BookingsPaymentsExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PaymentsAPIController extends AppBaseController
{
    /** @var  BookingRepository */
    private $bookingRepository;

    public function __construct(BookingRepository $bookingRepo)
    {
        $this->bookingRepository = $bookingRepo;
        parent::__construct();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
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

    /**
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function export(Request $request)
	{
		return Excel::download(new BookingsPaymentsExport($this->bookingRepository, $request), 'payments-list.csv');
	}
}
