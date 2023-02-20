<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Facades\BraintreeProcessor;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class PreRecordedLesson extends Model
{
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'genre_id',
        'instructor_id',
        'description',
        'video',
        'preview',
        'title',
        'price',
        'duration'
    ];

    protected $table = 'pre_r_lessons';

    /**
     * @return BelongsTo
     */
    public function genre()
    {
        return $this->belongsTo(Genre::class, 'genre_id');
    }

    /**
     * @return BelongsTo
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * @return HasMany
     */
    public function files()
    {
        return $this->hasMany(PreRLessonFile::class, 'pre_r_lesson_id', 'id');
    }

    /**
     * @return string
     */
    public function getPreviewUrl()
    {
        // TODO test 'https://skillective.com'
//        if(config('app.env') == 'local') {
//            return 'https://skillective.com' . '/storage/' . 'videos/' . $this->instructor_id . '/' . $this->preview;
//        } else {
            return config('app.url') . '/storage/' . 'videos/' . $this->instructor_id . '/' . $this->preview;
//        }

    }

    /**
     * @return string
     */
    public function getVideoUrl()
    {// TODO test 'https://skillective.com'
        if(config('app.env') == 'local') {
            return 'https://skillective.com' . '/storage/' . 'videos/' . $this->instructor_id . '/' . $this->video;
        } else {
            return config('app.url') . '/storage/' . 'videos/' . $this->instructor_id . '/' . $this->video;
        }
    }

    /**
     * @param $price
     * @return string
     */
    public function getPreRecordedLessonServiceFeeAmount($price = null)
    {
        if ($price == null)
            $price = $this->price;

        $serviceFeeFixed = (float)Setting::getValue('skillective_service_pre_r_fixed', 0); // $
        $serviceFeePercent = (float)Setting::getValue('skillective_service_pre_r_percent', 0); // $

        $serviceFeePercent = ($price / 100) * $serviceFeePercent;

        $serviceFee = $serviceFeeFixed + $serviceFeePercent;

        return number_format((float)$serviceFee, 2, '.', '');
    }

    /**
     * @param $price
     * @param $serviceFees
     * @return string
     */
    public function getPreRecordedLessonPaymentProcessingFeeAmount($price = null, $serviceFees = 0)
    {
        if ($price == null)
            $price = $this->price;

        $braintreeProcessingFee = (float)Setting::getValue('braintree_processing_fee', 2.9); // %
        $braintreeTransactionFee = (float)Setting::getValue('braintree_transaction_fee', 0.3); // $

        $processorFee = (($price + $serviceFees) / 100) * $braintreeProcessingFee + $braintreeTransactionFee;

        return number_format((float)$processorFee, 2, '.', '');
    }

    /**
     * @param $user_repository
     * @param $request
     * @param $paymentMethodNonce
     * @param $student
     * @return PurchasedLesson
     * @throws \Exception
     */
    public function purchareLesson($user_repository, $request, $paymentMethodNonce, $student)
    {
		if(Auth::user() != null){
			$user_repository->updateUserData(Auth::user()->id, $request);
		}

        if ($paymentMethodNonce) {
            $device_data = $request->input('device_data', null);
            $paymentMethod = BraintreeProcessor::createPaymentMethod($student, $paymentMethodNonce, $device_data);
        } else {
            $paymentMethodToken = $request->input('payment_method_token', null);
            $paymentMethod = BraintreeProcessor::findPaymentMethod($paymentMethodToken);

            if (!$paymentMethod || $paymentMethod->customerId != $student->braintree_customer_id) {
                Log::error("Student {$student->id} booking : Payment method not defined");
                throw new \Exception('Payment method not defined');
            }
            $paymentMethod = ['token' => $paymentMethod->token, 'type' => BraintreeProcessor::_getPaymentMethodType($paymentMethod)];
        }

        if (!$paymentMethod) {
            throw new \Exception('Payment method not found');
        }

        $purchashedLesson = new PurchasedLesson();
        $purchashedLesson->pre_r_lesson_id = $request->input('pre_r_lesson_id', '');
        $purchashedLesson->student_id        = $student->id;
        $purchashedLesson->instructor_id        = $this->instructor_id;
        $purchashedLesson->price                = $this->price;
        $purchashedLesson->status            = PurchasedLesson::STATUS_PENDING;
        $purchashedLesson->payment_method_token    = $paymentMethod['token'];
        $purchashedLesson->payment_method_type    = $paymentMethod['type'];
        $service_fee = $this->getPreRecordedLessonServiceFeeAmount($this->price);
        $purchashedLesson->service_fee = $service_fee;
        $purchashedLesson->processor_fee = $this->getPreRecordedLessonPaymentProcessingFeeAmount($this->price, $service_fee);
        $instructorMerchantId = $this->instructor->bt_submerchant_id;

        $purchashedLesson->save();

        $transaction = BraintreeProcessor::createPurchasereLessonTransactionAndHoldInEscrow(
            $instructorMerchantId,
            $purchashedLesson->payment_method_token,
            $purchashedLesson,
            $purchashedLesson->service_fee,
            $purchashedLesson->processor_fee
        );

        $purchashedLesson->transaction_id        = $transaction->id;
        $purchashedLesson->transaction_status    = $transaction->status;
        $purchashedLesson->transaction_created_at    = now();
        $purchashedLesson->setStatusAttribute(PurchasedLesson::STATUS_ESCROW);
        $purchashedLesson->save();

        return $purchashedLesson;
    }
}
