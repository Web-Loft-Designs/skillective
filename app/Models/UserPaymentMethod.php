<?php

namespace App\Models;

use Eloquent as Model;
use App\Models\User;
use Auth;
use Log;

class UserPaymentMethod extends Model
{
    public $table = 'user_payment_methods';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'payment_method_type',
        'payment_method_token'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'user_id' => 'required',
        'payment_method_type' => 'required',
        'payment_method_token' => 'required'
    ];

	protected $hidden = [
		'created_at', 'updated_at'
	];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}