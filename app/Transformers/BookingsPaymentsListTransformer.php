<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Booking;

/**
 * Class BookingsPaymentsListTransformer.
 *
 * @package namespace App\Transformers;
 */
class BookingsPaymentsListTransformer extends TransformerAbstract
{
    /**
     * Transform the Booking entity.
     *
     * @param \App\Models\Booking $model
     *
     * @return array
     */
    public function transform(Booking $model)
    {

        $processorFee = $model->pp_processor_fee ?? 0;

		return [
			'id'				=> $model->id,
			'instructor_id'		=> $model->instructor_id,
			'student_id'		=> $model->student_id,
			'lesson_id'			=> $model->lesson_id,
			'student_name'		=> $model->student?->getName(),
			'instructor_name'	=> $model->instructor?->getName(),
			'spot_price'		=> $model->spot_price,
			'amount_to_pay'		=> array_sum([$model->spot_price, $model->service_fee, $model->virtual_fee]),
			'fees_amount'		=> round(array_sum([$model->service_fee, $processorFee, $model->virtual_fee]),2),
			'status'			=> Booking::getStatusTitle($model->status),
			'created_at'		=> $model->created_at->format('M d, Y, h:i a'),
			'transaction_created_at' => $model->transaction_created_at?$model->transaction_created_at->format('M d, Y, h:i a') : '',
			'updated_at'		=> $model->updated_at->format('M d, Y, h:i a'),
		];
    }
}
