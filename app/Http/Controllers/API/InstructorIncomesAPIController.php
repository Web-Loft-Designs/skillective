<?php

namespace App\Http\Controllers\API;

use App\Repositories\LessonRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;
use Auth;
use Log;
use App\Facades\IncomesCalculator;
use App\Repositories\BookingRepository;
use App\Repositories\PurchasedLessonRepository;

class InstructorIncomesAPIController extends AppBaseController
{
    public function index($year = '', BookingRepository $booking_repository, LessonRepository $lesson_repository, PurchasedLessonRepository $preRPurshRepo)
    {
        if (!(is_numeric($year) || $year == ''))
            $this->sendResponse([]);

        $user = Auth::user();
        //		dd($lesson_repository->getCountBookedLessonsForPeriod($user->id, $year));
        //		dd($booking_repository->getAmountBookedForPeriod($user->id, $year));

        $incomes = IncomesCalculator::getInstructorIncomes($user, $year, $booking_repository, $lesson_repository, $preRPurshRepo);
        return $this->sendResponse($incomes);
    }
}
