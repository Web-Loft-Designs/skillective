<?php

namespace App\Criteria;

use App\Models\Booking;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class SpecificBookingCriteria.
 *
 * @package namespace App\Criteria;
 */
class SpecificBookingCriteria implements CriteriaInterface
{
	protected $bookingId;

	public function __construct($bookingId)
	{
		$this->bookingId = $bookingId;
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
		$model = $model->where('bookings.id', $this->bookingId);
        return $model;
    }
}
