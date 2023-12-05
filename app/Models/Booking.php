<?php

namespace App\Models;

use App\Facades\PayPalProcessor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Contracts\Transformable;
use Exception;
use Throwable;

class Booking extends Model implements Transformable
{
    use SoftDeletes;

    public $table = 'bookings';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const STATUS_PENDING = 'pending'; // waiting merchant approval
    const STATUS_APPROVED = 'approved'; // NOT USED  merchant approved booking / payment collected by marketplace account
    const STATUS_ESCROW = 'payment_in_escrow'; // money on marketplace account
    const STATUS_ESCROW_RELEASED = 'payment_released'; // money transferring to merchant account
    const STATUS_UNABLE_ESCROW_RELEASE = 'payment_release_error'; // can't release transaction, reason sent to admin emails
    const STATUS_COMPLETE = 'complete'; // money moved to merchant account
    const STATUS_CANCELLED = 'cancelled'; // merchant cancelled booking , money must go back to student

    protected $dates = ['deleted_at', 'transaction_created_at'];

    public $fillable = [
        'instructor_id',
        'student_id',
        'spot_price',
        'special_request',
        'lesson_id',
        'disconnected', // wheter room participant was refused from video lesson
//        'pp_reference_id',
//        'pp_processor_fee'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'instructor_id' => 'integer',
        'student_id' => 'integer',
        'lesson_id' => 'integer',
        'spot_price' => 'float',
        'special_request' => 'string',
        'has_cancellation_request' => 'boolean',
        'cancellation_request_created_at' => 'datetime',
        'disconnected' => 'boolean'
    ];

    /**
     * Additional observable events.
     */
    protected $observables = [
        'statusChanged',
    ];

    /**
     * @return BelongsTo
     **/
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    public function regularNotifications(): HasMany
    {
        return $this->hasMany(RegularNotification::class, 'user_regular_notifications');
    }

    /**
     * @return BelongsTo
     **/
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * @return BelongsTo
     **/
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function transform(): array
    {
        return [
            'id' => $this->id,
            'instructor_id' => $this->instructor_id,
            'student_id' => $this->student_id,
            'lesson_id' => $this->lesson_id,
            'student' => $this->student->transform(),
            'instructor' => $this->instructor->transform(),
            'lesson' => $this->lesson->transform(),
            'spot_price' => $this->spot_price,
            'special_request' => $this->special_request,
            'status' => $this->status,
            'has_cancellation_request' => $this->has_cancellation_request,
            'disconnected' => $this->disconnected
        ];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING,
//			self::STATUS_APPROVED,
            self::STATUS_ESCROW,
            self::STATUS_ESCROW_RELEASED,
            self::STATUS_UNABLE_ESCROW_RELEASE,
            self::STATUS_CANCELLED,
            self::STATUS_COMPLETE
        ];
    }

    public static function getStatusTitle($status): string
    {
        switch ($status) {
            case self::STATUS_ESCROW:
                return 'In Escrow';
                break;
            case self::STATUS_ESCROW_RELEASED:
                return 'Released from Escrow';
                break;
            case self::STATUS_UNABLE_ESCROW_RELEASE:
                return 'Unable release from Escrow';
                break;
            case self::STATUS_CANCELLED:
                return 'Cancelled';
                break;
            case self::STATUS_COMPLETE:
                return 'Complete';
                break;
            default:
                return ucfirst(str_replace('_', ' ', $status));
        }
    }

    public function setStatusAttribute($status): void
    {
        if (in_array($status, self::getStatuses()) && $this->status !== $status) {
            $this->attributes['status'] = $status;
        }
    }

    public function save(array $options = array())
    {
        $statusChanged = $this->isDirty('status') ? true : false;

        parent::save($options);

        if ($statusChanged) {
            $this->fireModelEvent('statusChanged');
        }
    }

    public function saveQuietly(array $options = [])
    {
        return static::withoutEvents(function () use ($options) {
            return $this->save($options);
        });
    }

    public function cancel($cancelledBy): bool
    {
        // few checks to prevent not desired transactions , just an assurance
        if ((!$this->lesson->alreadyStarted() && $this->status != self::STATUS_CANCELLED) || ($this->lesson->alreadyStarted() && $this->status == self::STATUS_PENDING)) {
            if ($this->transaction_id) {
                try {
                    PayPalProcessor::cancelTransaction($this->transaction_id);
                } catch (Exception|Throwable $e) {
                    Log::channel('paypal')->error('Can\'t cancel transaction. Transaction ' . $this->transaction_id . ' not found');
                    throw new Exception('Can\'t cancel transaction. Transaction ' . $this->transaction_id . ' not found');
                }
            }
            $this->setStatusAttribute(self::STATUS_CANCELLED);
            $this->transaction_status = 'cancelled';
            $this->has_cancellation_request = 0;
            if ($cancelledBy) {
                $this->cancelled_by = $cancelledBy;
            }
            $this->save();

            return true;
        } else {
            $reason = '';
            if ($this->lesson->alreadyStarted())
                $reason = "Lesson already started";
            elseif ($this->status == self::STATUS_CANCELLED)
                $reason = "Already cancelled";

            throw new \Exception('Booking #' . $this->id . ' can\'t be cancelled: ' . $reason, 400);
        }
    }

    public function autoCancel()
    {
        $cancelledBy = null;
        $this->cancel($cancelledBy);
    }


    public function getBookingServiceFeeAmount($spotPrice = null): float
    {
        if ($spotPrice == null) {
            $spotPrice = $this->spot_price;
        }

        $serviceFeeFixed = (float)Setting::getValue('skillective_service_fee_fixed', 0); // $
        $serviceFeePercent = (float)Setting::getValue('skillective_service_fee_percent', 0); // $

        $serviceFeePercent = ($spotPrice / 100) * $serviceFeePercent;

        $serviceFee = $serviceFeeFixed + $serviceFeePercent;

        return number_format((float)$serviceFee, 2, '.', '');
    }

    public function getBookingPaymentProcessingFeeAmount($spotPrice = null, $serviceFees = 0)
    {
        if ($spotPrice == null) {
            $spotPrice = $this->spot_price;
        }
        $braintreeProcessingFee = (float)Setting::getValue('braintree_processing_fee', 2.99); // %
        $braintreeTransactionFee = (float)Setting::getValue('braintree_transaction_fee', 0.49); // $
        $processorFee = (($spotPrice + $serviceFees) / 100) * $braintreeProcessingFee + $braintreeTransactionFee;

        return number_format((float)$processorFee, 2, '.', '');
    }

    public function getBookingVirtualFeeAmount(Lesson $lesson = null)
    {
        if ($lesson == null)
            $lesson = $this->lesson;

        $virtualLessonFee = 0;
        if ($lesson->lesson_type == 'virtual') {
            $roomType = $lesson->getRoomType();

            if ($roomType == 'group') {
                $twilioMinutePrice = (float)Setting::getValue('twilio_group_fee', 0.01);
            } else {
                $twilioMinutePrice = (float)Setting::getValue('twilio_small_group_fee', 0.004);
            }

            $expectedVideoDuration = $lesson->end->diffInMinutes($lesson->start);
            $expectedVideoDuration += 5 + 1; // auto start 5 min before lesson start time + auto end in 1 min after lesson end time
            $virtualLessonFee = $twilioMinutePrice * $expectedVideoDuration;
        }

        return number_format((float)$virtualLessonFee, 2, '.', '');
    }

    public function getBookingTotalFeeAmount(Lesson $lesson = null, $spotPrice = null)
    {
        $serviceFee = $this->getBookingServiceFeeAmount($spotPrice);
        $virtualLessonFee = $this->getBookingVirtualFeeAmount($lesson);

        $processorFee = $this->getBookingPaymentProcessingFeeAmount($spotPrice, ($serviceFee + $virtualLessonFee));
        $totalFee = $serviceFee + $processorFee + $virtualLessonFee;

        return number_format((float)$totalFee, 2, '.', '');
    }

    public function approvePp(): bool
    {
        $serviceFee = $this->getBookingServiceFeeAmount();
        $virtualLessonFee = $this->getBookingVirtualFeeAmount();
        $totalServiceFee = (float) $serviceFee + (float) $virtualLessonFee;
        $processorFee = $this->getBookingPaymentProcessingFeeAmount($this->spot_price, $totalServiceFee);

        // few checks to prevent not desired transactions , just an assurance
        if (!$this->transaction_id && ($this->instructor->pp_merchant_id) != null
            && $this->instructor->pp_account_status == PayPalProcessor::STATUS_ACTIVE && !$this->lesson->alreadyStarted()
            && !$this->lesson->is_cancelled && $this->status == self::STATUS_PENDING) {

            $transaction = PayPalProcessor::createSellBookingTransactionAndHoldInEscrow(
                $this->payment_method_token,
                $this,
                $totalServiceFee,
                $processorFee
            );

            $this->transaction_id = $transaction['id'];
            $this->transaction_status = $transaction['status'];
            $this->transaction_created_at = now();
            $this->service_fee = $serviceFee;
            $this->processor_fee = $processorFee;  //  налог прогнозований
            $this->pp_reference_id = $transaction['purchase_units'][0]['payments']['captures'][0]['id'];
            $this->pp_processor_fee = $transaction['purchase_units'][0]['payments']['captures'][0]['seller_receivable_breakdown']['paypal_fee']['value']; // налог отриманий фактичний
            $this->virtual_fee = $virtualLessonFee;
            $this->setStatusAttribute(self::STATUS_ESCROW);
            $this->save();
        } else {
            $reason = '';
            if ($this->transaction_id)
                $reason = "Payment already sent";
            elseif ($this->instructor->pp_merchant_id == null)
                $reason = "No merchant account provided. Please check Profile settings";
            elseif ($this->instructor->pp_account_status != "BUSINESS_ACCOUNT")
                $reason = "Merchant account not active";
            elseif ($this->lesson->alreadyStarted())
                $reason = "Lesson already started";
            elseif ($this->lesson->is_cancelled)
                $reason = "Lesson already cancelled";
            elseif ($this->status != self::STATUS_PENDING)
                $reason = "It is not a pending booking";

            throw new \Exception('Booking #' . $this->id . ' can\'t be approved: ' . $reason, 400);
        }
        return true;
    }
}
