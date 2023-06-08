<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromoCode extends Model
{
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'name',
    	'instructor_id',
        'title',
        'lesson_type',
        'discount_type',
        'discount',
        'lessons_for_apply',
        'start',
        'finish',
        'users_count',
        'used_time',
        'used_with_other_discounts'
    ];

    protected $table = 'promo_codes';

    /**
     * @return BelongsTo
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
}
