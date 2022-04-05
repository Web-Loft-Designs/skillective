<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CancelBookingsAPIRequest;
use App\Http\Requests\API\ShareBookingsScheduleAPIRequest;
use App\Models\User;
use App\Models\Booking;
use App\Repositories\BookingRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;
use Auth;
use Log;
use App\Notifications\Share\ShareBookingsSchedule;
use App\Notifications\Bookings\BookingCancellationRequestInstructorNotification;
use Notification;
use App\Exports\BookingsExport;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class BookingController
 * @package App\Http\Controllers\API
 */

class StudentBookingsAPIController extends AppBaseController
{
    /** @var  BookingRepository */
    private $bookingRepository;

    public function __construct(BookingRepository $bookingRepo)
    {
        $this->bookingRepository = $bookingRepo;
    }

    /**
     * Display a listing of the Booking.
     * GET|HEAD /bookings
     *
     * @param Request $request
     * @return Response
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

	public function export(Request $request)
	{
		$this->bookingRepository->setPresenter("App\\Presenters\\BookingInListPresenter");
		$local_time = strtotime($request->get('local_time', 'now'));
		return Excel::download(new BookingsExport($this->bookingRepository, $request, Auth::user()->id, $local_time), 'bookings-list.xlsx');
	}
    /**
     * Remove the specified Booking from storage.
     * DELETE /bookings/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function cancel(Booking $booking)
    {
		$this->_requestCancellation($booking);

        return $this->sendResponse(true, 'Booking cancellation request sent to instructor');
    }

	public function cancelMany(CancelBookingsAPIRequest $request)
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

	private function _requestCancellation(Booking $booking){
		$booking->has_cancellation_request = 1;
		$booking->cancellation_request_created_at = now();
		$booking->save();

		try{
			$booking->instructor->notify(new BookingCancellationRequestInstructorNotification($booking));
		}catch (\Exception $e){
			Log::error("BookingCancellationRequestInstructorNotification Error: " . $e->getCode() . ': ' . $e->getMessage());
		}

		return true;
	}
}
