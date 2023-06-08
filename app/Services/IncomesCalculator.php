<?php

namespace App\Services;

use App\Repositories\LessonRepository;
use App\Repositories\BookingRepository;
use App\Repositories\PurchasedLessonRepository;

class IncomesCalculator
{

	private $booking_repository = null;
	private $purchasedLessons = null;

	public function __construct(BookingRepository $booking_repository, PurchasedLessonRepository $purshPreRLessonRepo)
	{
		$this->booking_repository = $booking_repository;
		$this->purchasedLessons = $purshPreRLessonRepo;
	}

    /**
     * @param $instructorId
     * @return int|mixed
     */
    public function totalAmountInEscrow($instructorId)
	{
		$preREarned = $this->purchasedLessons->getAmountEarnedForPeriod($instructorId);
		
		$earnedVal = $preREarned ? array_values($preREarned)[0] : 0;

		return ($this->booking_repository->totalAmountInEscrow($instructorId) + $earnedVal);
	}


    /**
     * @param $instructor
     * @param $period
     * @param BookingRepository $bookingsRepository
     * @param LessonRepository $lessonRepository
     * @param PurchasedLessonRepository $purshPreRLessonRepo
     * @return array
     */
    public function getInstructorIncomes($instructor, $period = '', BookingRepository $bookingsRepository, LessonRepository $lessonRepository, PurchasedLessonRepository $purshPreRLessonRepo)
	{
		$countBookedLessons = $lessonRepository->getCountBookedLessonsForPeriod($instructor->id, $period);
		$countHeldLessons = $lessonRepository->getCountCompleteLessonsForPeriod($instructor->id, $period);
		$amountEarned = $bookingsRepository->getAmountEarnedForPeriod($instructor->id, $period);
		$amountBooked = $bookingsRepository->getAmountBookedForPeriod($instructor->id, $period);

		$preREarned = $purshPreRLessonRepo->getAmountEarnedForPeriod($instructor->id, $period);

		$preRCount = $purshPreRLessonRepo->getCountPurchasesLessonsForPeriod($instructor->id, $period);

		$instructorRegistrationYear = $instructor->created_at->format('Y');
		$incomes = [];
		if ($period == '') { // all time
			$currentYear = date('Y');
			for ($y = $instructorRegistrationYear; $y <= $currentYear; $y++) {

				$earnedByPreR = isset($preREarned[$y]) ? $preREarned[$y] : 0;

				$income = [
					'startDate' => "$y-01-01",
					'countBookedLessons' => isset($countBookedLessons[$y]) ? $countBookedLessons[$y] : 0,
					'countHeldLessons' => isset($countHeldLessons[$y]) ? $countHeldLessons[$y] : 0,
					'amountEarned' => isset($amountEarned[$y]) ? $amountEarned[$y] + $earnedByPreR : 0 + $earnedByPreR,
					'amountBooked' => isset($amountBooked[$y]) ? $amountBooked[$y] : 0,
					'preRPurchases' => isset($preRCount[$y]) ? $preRCount[$y] : 0,
				];
				$incomes[] = $income;
			}
		} elseif (is_numeric($period)) {
			$lastMonth = 12; //date('Y')==$period ? date('n') : 12;
			for ($m = 1; $m <= $lastMonth; $m++) {
				$month = sprintf('%02d', $m);

				$earnedByPreR = isset($preREarned[$m]) ? $preREarned[$m] : 0;

				$income = [
					'startDate' => "$period-$month-01",
					'countBookedLessons' => isset($countBookedLessons[$m]) ? $countBookedLessons[$m] : 0,
					'countHeldLessons' => isset($countHeldLessons[$m]) ? $countHeldLessons[$m] : 0,
					'amountEarned' => isset($amountEarned[$m]) ? $amountEarned[$m] + $earnedByPreR : 0 + $earnedByPreR,
					'amountBooked' => isset($amountBooked[$m]) ? $amountBooked[$m] : 0,
					'preRPurchases' => isset($preRCount[$m]) ? $preRCount[$m] : 0,
				];
				$incomes[] = $income;
			}
		}

		return $incomes;
	}
}
