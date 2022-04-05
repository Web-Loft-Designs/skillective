<?php

namespace App\Criteria;

use App\Models\Lesson;
use App\Models\Booking;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
/**
 * Class InstructorLessonDashboardTypeCriteria.
 *
 * @package namespace App\Criteria;
 */
class InstructorLessonDashboardTypeCriteria implements CriteriaInterface
{
	protected $lessonType;

	public function __construct($lessonType)
	{
		$this->lessonType = $lessonType;
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
		switch ($this->lessonType) {
			case 'past':
				$model = $model->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) >= lessons.end")
                    ->where('bookings.status', '<>', Booking::STATUS_CANCELLED);
                break;
			case 'all':
                $model = $model->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) < lessons.end");
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
