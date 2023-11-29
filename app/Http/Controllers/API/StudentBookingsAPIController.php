<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CancelBookingsAPIRequest;
use App\Http\Requests\API\ShareBookingsScheduleAPIRequest;
use App\Models\Booking;
use App\Repositories\BookingRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Notifications\Share\ShareBookingsSchedule;
use App\Notifications\Bookings\BookingCancellationRequestInstructorNotification;
use App\Exports\BookingsExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class StudentBookingsAPIController extends AppBaseController
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
			$this->bookingRepository->setPresenter("App\\Presenters\\BookingInListPresenter");
			$bookings = $this->bookingRepository->presentResponse($this->bookingRepository->getStudentBookings($request, Auth::user()->id));
		}catch (\Exception $e){
			Log::error('getStudentBookings : ' . $e->getMessage());
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
		$this->bookingRepository->setPresenter("App\\Presenters\\BookingInListPresenter");
		$local_time = strtotime($request->get('local_time', 'now'));
		return Excel::download(new BookingsExport($this->bookingRepository, $request, Auth::user()->id, $local_time), 'bookings-list.xlsx');
	}

    /**
     * @param Booking $booking
     * @return JsonResponse
     */
    public function cancel(Booking $booking): JsonResponse
    {
		$this->_requestCancellation($booking);
        return $this->sendResponse(true, 'Booking cancellation request sent to instructor');
    }

    /**
     * @param CancelBookingsAPIRequest $request
     * @return JsonResponse
     */
    public function cancelMany(CancelBookingsAPIRequest $request): JsonResponse
    {
		$count_cancelled = 0;
		foreach ($request->input('bookings') as $bookingId){
			$booking = $this->bookingRepository->findWithoutFail($bookingId);
			if ($booking){
				$this->_requestCancellation($booking);
				$count_cancelled++;
			}
		}

		return $this->sendResponse(true, $count_cancelled . ' cancellation requests sent');
	}

    /**
     * @param ShareBookingsScheduleAPIRequest $request
     * @return JsonResponse
     */
    public function share(ShareBookingsScheduleAPIRequest $request){
    	$currentUser = Auth::user();
		try{
			Notification::route('mail', $request->email)->notify(new ShareBookingsSchedule($currentUser, $this->bookingRepository)); // $currentUser->notify(new ShareBookingsSchedule($currentUser, $this->bookingRepository));
		}catch (\Exception $e){
			Log::error("ShareBookingsSchedule Error: " . $e->getCode() . ': ' . $e->getMessage());
			return $this->sendError('Can\'t share your schedule!', 400);
		}
		return $this->sendResponse(true, 'iCalendar file sent to provided email');
	}

    /**
     * @param Booking $booking
     * @return true
     */
    private function _requestCancellation(Booking $booking): bool
    {
		$booking->has_cancellation_request = 1;
		$booking->cancellation_request_created_at = now();
		$booking->save();

		try{
			$booking->instructor->notify(new BookingCancellationRequestInstructorNotification($booking));
		} catch (\Exception $e) {
			Log::error("BookingCancellationRequestInstructorNotification Error: " . $e->getCode() . ': ' . $e->getMessage());
		}
		return true;
	}
}
