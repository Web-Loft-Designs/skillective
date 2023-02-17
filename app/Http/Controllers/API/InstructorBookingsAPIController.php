<?php

namespace App\Http\Controllers\API;


use App\Http\Requests\API\CancelBookingsAPIRequest;
use App\Repositories\BookingRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class InstructorBookingsAPIController extends AppBaseController
{
    /** @var  BookingRepository */
    private $bookingRepository;

    public function __construct(BookingRepository $bookingRepo)
    {
        parent::__construct();
        $this->bookingRepository = $bookingRepo;
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
		try{
            $this->bookingRepository->setPresenter("App\\Presenters\\BookingInListPresenter");
			$bookings = $this->bookingRepository->presentResponse($this->bookingRepository->getInstructorBookings($request, Auth::user()->id));
		}catch (\Exception $e){
			Log::error('getInstructorBookings : ' . $e->getMessage());
			$bookings = ['data'=>[]];
		}

        return $this->sendResponse($bookings);
	}


    /**
     * @param $id
     * @return JsonResponse
     * @throws \Exception
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

    /**
     * @param CancelBookingsAPIRequest $request
     * @return JsonResponse
     */
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

    /**
     * @param $id
     * @return JsonResponse
     */
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
