<?php

namespace App\Notifications;

use App\Models\CustomNotification;
use App\Models\PromoCode;

class StudentPromoNotification extends AbstractCustomNotification
{
    /**
     * @var \App\Models\Booking
     */
    private $booking;

    public function __construct(PromoCode $promo)
    {
        $this->promo_code = $promo;

        parent::__construct();
    }

    public function variables()
    {
        $amount = $this->promo_code->discount_type == 'fixed-amount' ? '$' . $this->promo_code->discount : $this->promo_code->discount . '%';

        $lesson_type = "";

        switch ($this->promo_code->lesson_type) {
            case 'all':
                $lesson_type = 'All';
                break;
            case 'virtual':
                $lesson_type = 'Virtual';
                break;
            case 'in-person':
                $lesson_type = 'In-Person';
                break;
            case 'in-person':
                $lesson_type = 'In-Person';
                break;
            case 'pre-recorded':
                $lesson_type = 'Pre-Recorded';
                break;
        }

        return [
            'promo_name'                => $this->promo_code->name,
            'instructor_name'    => $this->promo_code->instructor->getName(),
            'amount' => $amount,
            'lesson_type' => $lesson_type,
            'finish' => $this->promo_code->finish
        ];
    }
    /**
     * @return \App\Models\CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return CustomNotification::query()->whereTag('new_promo_code')->first();
    }
}
