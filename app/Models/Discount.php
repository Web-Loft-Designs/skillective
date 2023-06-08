<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Discount extends Model
{
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
    	'instructor_id',
        'name',
        'title',
        'lesson_type',
        'discount_type',
        'lessons_for_apply',
        'discount',
        'start',
        'finish',
        'users_count',
        'used_time',
        'used_with_other_discounts'
    ];

    protected $table = 'discounts';

    /**
     * @return BelongsTo
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * @param $discounts
     * @param $cart
     * @return mixed
     */
    static function validateDiscount($discounts, $cart)
    {
        if($discounts) {
            foreach($discounts as $key => $discount) {
                if($discount->isActivate){
                    continue;
                }
                $itemsNeedToActivate = $discount->lessons_for_apply;
                foreach($cart as $cartKey => $cartItem){
                    if($cartItem->instructor_id != $discount->instructor_id) {
                        continue;
                    }
                    if($discount->lesson_type == 'all') {
                        $itemsNeedToActivate -= 1;
                        continue;
                    }else if($discount->lesson_type == 'pre-recorded' && $cartItem->pre_r_lesson_id) {
                        $itemsNeedToActivate -= 1;
                        continue;
                    }
                    else if(!$cartItem->pre_r_lesson_id && $discount->lesson_type == 'virtual' && $cartItem->lesson->lesson_type == 'virtual') {
                        $itemsNeedToActivate -= 1;
                        continue;
                    }
                    else if(!$cartItem->pre_r_lesson_id && $discount->lesson_type == 'in-person' && $cartItem->lesson->lesson_type == 'in_person') {
                        $itemsNeedToActivate -= 1;
                        continue;
                    }
                }
                if ($itemsNeedToActivate <= 0) {
                    $discounts[$key]->isActivate = true;
                } else {
                    $discounts[$key]->itemsLeft = $itemsNeedToActivate;
                }
            }
        }

        return $discounts;
    }


}
