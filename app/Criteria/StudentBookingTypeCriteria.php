<?php

namespace App\Criteria;

use App\Models\Booking;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use Carbon\Carbon;

/**
 * Class StudentBookingTypeCriteria.
 *
 * @package namespace App\Criteria;
 */
class StudentBookingTypeCriteria implements CriteriaInterface
{
	protected $bookingType;

	public function __construct($bookingType)
	{
		$this->bookingType = $bookingType;
	}

    /**
     * Apply criteria in query repository
     *
     * @param string              $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
		$nowOnServer = Carbon::now()->format('Y-m-d H:i:s'); // UTC
		switch ($this->bookingType){
			case 'past':
				$model = $model->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) >= lessons.end")
							   ->where('bookings.status', '<>', Booking::STATUS_CANCELLED);
				break;
			case 'cancelled':
				$model = $model->where('bookings.status', Booking::STATUS_CANCELLED);
				break;
			case 'pending':
				$model = $model->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) < lessons.start")
							   ->where('bookings.status', Booking::STATUS_PENDING);
				break;
			case 'pending_cancellation':
				$model = $model->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) < lessons.start")
							   ->where('bookings.status', '<>', Booking::STATUS_CANCELLED)
							   ->where('bookings.has_cancellation_request', 1);
				break;
			case 'payed':
				$model = $model->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) < lessons.start")
							   ->where('bookings.status', Booking::STATUS_ESCROW);
				break;
			case 'active':
				$model = $model->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) < lessons.end")
						->whereNotIn('bookings.status', [Booking::STATUS_COMPLETE, Booking::STATUS_CANCELLED]);
				break;
			case 'approved':
			default:
				$model = $model->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) < lessons.end")
							   ->whereIn('bookings.status', [Booking::STATUS_APPROVED, Booking::STATUS_ESCROW]);
				break;
		}

        return $model;
    }
}
