<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CancelBookingsAPIRequest;
use App\Repositories\BookingRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;
use Auth;
use Log;
use App\Models\Booking;

/**
 * Class BookingController
 * @package App\Http\Controllers\API
 */

class InstructorBookingsAPIController extends AppBaseController
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
			$bookings = $this->bookingRepository->presentResponse($this->bookingRepository->getInstructorBookings($request, Auth::user()->id));
		}catch (\Exception $e){
			\Log::error('getInstructorBookings : ' . $e->getMessage());
			$bookings = ['data'=>[]];
		}

        return $this->sendResponse($bookings);
	}

    /**
     * Remove the specified Booking from storage.
     * DELETE /bookings/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function cancel($id)
    {
        /** @var Booking $booking */
        $booking = $this->bookingRepository->findWithoutFail($id);

        if (empty($booking)) {
            return $this->sendError('Booking not found');
        }
		$cancelledBy = Auth::user()->id;
		$booking->cancel($cancelledBy);

        return $this->sendResponse(1, 'Booking cancelled');
    }

	public function cancelMany(CancelBookingsAPIRequest $request)
	{
		$errors = [];
		$count_cancelled = 0;
		foreach ($request->input('bookings') as $bookingId){
			try{
				$booking = $this->bookingRepository->findWithoutFail($bookingId);
				$cancelledBy = Auth::user()->id;
				$booking->cancel($cancelledBy);
				$count_cancelled++;
			}catch (\Exception $e){
				$errors[] = trim($e->getMessage(), '<br>');
			}
		}

		$message = $count_cancelled . ' bookings cancelled.';
		if (count($errors)){
			$message .= '<br>' . implode('<br>', $errors);
		}
		return $this->sendResponse($count_cancelled, $message);
	}

	public function approve($id)
	{
		/** @var Booking $booking */
		$booking = $this->bookingRepository->findWithoutFail($id);

		if (empty($booking) || $booking->status!=Booking::STATUS_PENDING) {
			return $this->sendError('Booking not found');
		}

		if ( $booking->lesson->is_cancelled ){
			return $this->sendError('Lesson cancelled');
		}
		if ($booking->lesson->alreadyStarted()){
			return $this->sendError('Lesson already started. You can\'t approve.');
		}

		try{

			$booking->approve();

		}catch (\Exception $e){
			Log::error('Booking #' . $id . ' approval error:' . $e->getMessage());
			//'Payment can\'t be processed. The payment method choosen while booking this lesson is not available.'
			return $this->sendError('Booking #' . $id . ' approval error:(' . $e->getCode() . ')'. $e->getMessage());
		}

		return $this->sendResponse($booking->transform(), 'Booking confirmed');
	}
}
