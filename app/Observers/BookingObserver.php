<?php

namespace App\Observers;

use App\Models\Booking;
use App\Models\Cart;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;
use Log;
use App\Repositories\UserRepository;

use App\Notifications\Bookings\BookingCreatedStudentConfirmation;
use App\Notifications\Bookings\BookingCreatedInstructorNotification;

use App\Notifications\Bookings\BookingApprovedStudentNotification;

use App\Notifications\Bookings\BookingPaymentInEscrowStudentNotification;
use App\Notifications\Bookings\BookingPaymentInEscrowInstructorNotification;
use App\Notifications\Bookings\BookingPaymentInEscrowAdminNotification;

//use App\Notifications\Bookings\BookingPaymentDisbursedStudentNotification;
use App\Notifications\Bookings\BookingPaymentDisbursedInstructorNotification;
use App\Notifications\Bookings\BookingPaymentDisbursedAdminNotification;

use App\Notifications\Bookings\BookingCancelledStudentNotification;
use App\Notifications\Bookings\BookingCancelledInstructorNotification;
use App\Notifications\Bookings\BookingCancelledAdminNotification;

use App\Notifications\Bookings\BookingAutomaticallyCancelledStudentNotification;
use App\Notifications\Bookings\BookingAutomaticallyCancelledInstructorNotification;

use App\Notifications\Bookings\BookingCantReleaseTransactionAdminNotification;


class BookingObserver
{
    /** @var  UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepo) {
        $this->userRepository = $userRepo;
    }

    public function statusChanged(Booking $booking)
    {
        switch ($booking->status){
            case Booking::STATUS_PENDING:
                try{
                    //$booking->instructor->notify(new BookingCreatedInstructorNotification($booking));
                }catch (\Exception $e){
                    Log::error("BookingCreatedInstructorNotification Error for #{$booking->instructor->id} : " . $e->getCode() . ': ' . $e->getMessage());
                }
                try{
                    //$booking->student->notify(new BookingCreatedStudentConfirmation($booking));
                }catch (\Exception $e){
                    Log::error("BookingCreatedStudentConfirmation Error for #{$booking->student->id} : " . $e->getCode() . ': ' . $e->getMessage());
                }
                break;
            case Booking::STATUS_APPROVED:
                try{
                    //$booking->student->notify(new BookingApprovedStudentNotification($booking));
                }catch (\Exception $e){
                    Log::error("BookingApprovedStudentNotification Error for #{$booking->student->id} : " . $e->getCode() . ': ' . $e->getMessage());
                }
                break;
            case Booking::STATUS_ESCROW:
                try{
                    $booking->student->notify(new BookingPaymentInEscrowStudentNotification($booking));
                }catch (\Exception $e){
                    Log::error("BookingPaymentInEscrowStudentNotification Error for #{$booking->student->id} : " . $e->getCode() . ': ' . $e->getMessage());
                }
                try{
                    $booking->instructor->notify(new BookingPaymentInEscrowInstructorNotification($booking));
                }catch (\Exception $e){
                    Log::error("BookingPaymentInEscrowInstructorNotification Error for #{$booking->instructor->id} : " . $e->getCode() . ': ' . $e->getMessage());
                }
                // notify admins
                $administrators = $this->userRepository->getAdministrators();
                foreach ($administrators as $administrator) {
                    try{
                        $administrator->notify(new BookingPaymentInEscrowAdminNotification($booking));
                    }catch (\Exception $e){
                        Log::error("BookingPaymentInEscrowAdminNotification Error for #{$administrator->id} : " . $e->getCode() . ': ' . $e->getMessage());
                    }
                }
                break;
            case Booking::STATUS_COMPLETE:
                try{
                    $booking->instructor->notify(new BookingPaymentDisbursedInstructorNotification($booking));
                }catch (\Exception $e){
                    Log::error("BookingPaymentDisbursedInstructorNotification Error for #{$booking->instructor->id} : " . $e->getCode() . ': ' . $e->getMessage());
                }

                // notify admins
                $administrators = $this->userRepository->getAdministrators();
                foreach ($administrators as $administrator) {
                    try{
                        $administrator->notify(new BookingPaymentDisbursedAdminNotification($booking));
                    }catch (\Exception $e){
                        Log::error("BookingPaymentDisbursedAdminNotification Error for #{$administrator->id} : " . $e->getCode() . ': ' . $e->getMessage());
                    }
                }
                break;
            case Booking::STATUS_UNABLE_ESCROW_RELEASE:
                // try{
                // 	$booking->instructor->notify(new BookingCantReleaseTransactionAdminNotification($booking));
                // }catch (\Exception $e){
                // 	Log::error("BookingCantReleaseTransactionAdminNotification Error for #{$booking->instructor->id} : " . $e->getCode() . ': ' . $e->getMessage());
                // }

                $administrators = $this->userRepository->getAdministrators();
                foreach ($administrators as $administrator) {
                    try{
                        $administrator->notify(new BookingCantReleaseTransactionAdminNotification($booking));
                    }catch (\Exception $e){
                        Log::error("BookingCantReleaseTransactionAdminNotification Error for #{$administrator->id} : " . $e->getCode() . ': ' . $e->getMessage());
                    }
                }
                break;
            case Booking::STATUS_CANCELLED:
                if($booking->cancelled_by==null){
                    try{
                        $booking->student->notify(new BookingAutomaticallyCancelledStudentNotification($booking));
                    }catch (\Exception $e){
                        Log::error("BookingAutomaticallyCancelledStudentNotification Error for #{$booking->student->id} : " . $e->getCode() . ': ' . $e->getMessage());
                    }

                    try{
                        $booking->instructor->notify(new BookingAutomaticallyCancelledInstructorNotification($booking));
                    }catch (\Exception $e){
                        Log::error("BookingAutomaticallyCancelledInstructorNotification Error for #{$booking->instructor->id} : " . $e->getCode() . ': ' . $e->getMessage());
                    }

                }else{
                    try{
                        $booking->student->notify(new BookingCancelledStudentNotification($booking));
                    }catch (\Exception $e){
                        Log::error("BookingCancelledStudentNotification Error for #{$booking->student->id} : " . $e->getCode() . ': ' . $e->getMessage());
                    }
                    try{
                        $booking->instructor->notify(new BookingCancelledInstructorNotification($booking));
                    }catch (\Exception $e){
                        Log::error("BookingCancelledInstructorNotification Error for #{$booking->instructor->id} : " . $e->getCode() . ': ' . $e->getMessage());
                    }
                }
                // notify admins
                $administrators = $this->userRepository->getAdministrators();
                foreach ($administrators as $administrator) {
                    try{
                        $administrator->notify(new BookingCancelledAdminNotification($booking));
                    }catch (\Exception $e){
                        Log::error("BookingCancelledAdminNotification Error for #{$administrator->id} : " . $e->getCode() . ': ' . $e->getMessage());
                    }
                }
                break;
        }
    }

    /**
     * @param Booking $booking
     * @return void
     */
    public function created(Booking $booking)
    {



    }
}
