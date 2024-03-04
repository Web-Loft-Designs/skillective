<?php

namespace App\Http\Controllers\API;

use App\Repositories\LessonRepository;
use App\Http\Controllers\AppBaseController;
use App\Facades\IncomesCalculator;
use App\Repositories\BookingRepository;
use App\Repositories\PurchasedLessonRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class InstructorIncomesAPIController extends AppBaseController
{
    /**
     * @param  $year
     * @param BookingRepository $booking_repository
     * @param LessonRepository $lesson_repository
     * @param PurchasedLessonRepository $preRPurshRepo
     * @return JsonResponse
     */
    public function index($year = ' ', BookingRepository $booking_repository, LessonRepository $lesson_repository, PurchasedLessonRepository $preRPurshRepo)
    {
        if (!(is_numeric($year) || $year == '')) {
            $this->sendResponse([]);
        }

        $user = Auth::user();
        $incomes = IncomesCalculator::getInstructorIncomes($user, $year, $booking_repository, $lesson_repository, $preRPurshRepo);
        return $this->sendResponse($incomes);
    }
}
