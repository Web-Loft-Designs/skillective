<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

/**
 * Class BookingPayoutTransformer.
 *
 * @package namespace App\Transformers;
 */
class BookingPayoutTransformer extends TransformerAbstract
{
    /**
     * Transform the Booking entity.
     *
     * @param \App\Models\Booking $model
     *
     * @return array
     */
    public function transform($item)
    {
		return [
			'totalPayoutsAmount'	=> $item->totalPayoutsAmount,
			'payoutsPeriod'			=> $item->payoutsPeriod
		];
    }
}
