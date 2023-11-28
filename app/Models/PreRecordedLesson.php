<?php

namespace App\Models;

use App\Facades\PayPalProcessor;
use Illuminate\Database\Eloquent\Model;
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
    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class, 'genre_id');
    }

    /**
     * @return BelongsTo
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * @return HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(PreRLessonFile::class, 'pre_r_lesson_id', 'id');
    }

    /**
     * @return string
     */
    public function getPreviewUrl(): string
    {
        return config('app.url') . '/storage/' . 'videos/' . $this->instructor_id . '/' . $this->preview;
    }

    /**
     * @return string
     */
    public function getVideoUrl(): string
    {
        return config('app.url') . '/storage/' . 'videos/' . $this->instructor_id . '/' . $this->video;
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


    public function purchaseLessonPp($user_repository, $request, $paymentMethodNonce, $student): PurchasedLesson
    {
        if(Auth::user() != null) {
            $user_repository->updateUserData(Auth::user()->id, $request);
        }

        if ($paymentMethodNonce) {
            $paymentMethod = PayPalProcessor::createPaymentMethod($student, $paymentMethodNonce);
        } else {
            $paymentMethod = UserPaymentMethod::find($request->input('payment_method_token', null));
            if (!$paymentMethod || $paymentMethod->user_id != $student->id) {
                Log::channel('paypal')->error("Student {$student->id} booking : Payment method not defined" );
                throw new \Exception('Payment method not defined');
            }
            $paymentMethod = ['token' => $paymentMethod->payment_method_token, 'type' => $paymentMethod->payment_method_type];
        }
        if (!$paymentMethod) {
            throw new \Exception('Payment method not found');
        }

        $purchasedLesson = new PurchasedLesson();
        $purchasedLesson->pre_r_lesson_id         = $request->input('pre_r_lesson_id', '');
        $purchasedLesson->student_id              = $student->id;
        $purchasedLesson->instructor_id           = $this->instructor_id;
        $purchasedLesson->price                   = $this->price;
        $purchasedLesson->status                  = PurchasedLesson::STATUS_PENDING;
        $purchasedLesson->payment_method_token    = $paymentMethod['token'];
        $purchasedLesson->payment_method_type     = $paymentMethod['type'];
        $service_fee                              = $this->getPreRecordedLessonServiceFeeAmount($this->price);
        $purchasedLesson->service_fee             = $service_fee;
        $purchasedLesson->processor_fee           = $this->getPreRecordedLessonPaymentProcessingFeeAmount($this->price, $service_fee);
        $instructorMerchantId                     = $this->instructor->pp_merchant_id;

        $purchasedLesson->save();

        $transaction = PayPalProcessor::createSellPurchasereLessonTransaction(
            $instructorMerchantId,
            $purchasedLesson->payment_method_token,
            $purchasedLesson,
            $purchasedLesson->service_fee,
            $purchasedLesson->processor_fee
        );

        $purchasedLesson->transaction_id        = $transaction['id'];
        $purchasedLesson->transaction_status    = $transaction['status'];
        $purchasedLesson->transaction_created_at    = now();
        $purchasedLesson->setStatusAttribute(PurchasedLesson::STATUS_ESCROW);
        $purchasedLesson->save();

        return $purchasedLesson;
    }
}
