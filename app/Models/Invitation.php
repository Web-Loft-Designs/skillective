<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Prettus\Repository\Contracts\Transformable;

class Invitation extends Model implements Transformable
{
	use Notifiable, SoftDeletes;

    public $table = 'invitations';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'invited_by',
        'invited_as_instructor',
        'invited_name',
        'invited_email',
        'invited_instagram_handle',
        'invited_mobile_phone',
        'invited_user_id',
		'invitation_token'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'invited_by' => 'integer',
        'invited_as_instructor' => 'boolean',
        'invited_name' => 'string',
        'invited_email' => 'string',
        'invited_instagram_handle' => 'string',
		'invited_user_id' => 'integer'
    ];


    /**
     * @return BelongsTo
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }


    /**
     * @return BelongsTo
     */
    public function invitedUser()
	{
		return $this->belongsTo(User::class, 'invited_user_id');
	}

    /**
     * @param $val
     * @return void
     */
    public function setInvitedMobilePhoneAttribute($val){
		$this->attributes['invited_mobile_phone'] = preg_replace('/[\.\+\s\(\)]/', '', $val);
	}

    /**
     * @param $driver
     * @param $notification
     * @return MorphMany|mixed|string|void
     */
    public function routeNotificationFor($driver, $notification = null)
	{
		if (method_exists($this, $method = 'routeNotificationFor'.Str::studly($driver))) {
			return $this->{$method}($notification);
		}

		switch ($driver) {
			case 'database':
				return $this->notifications();
			case 'mail':
				return $this->invited_email;
			case 'nexmo':
				return $this->invited_mobile_phone;
			case 'sms':
				return prepareMobileForTwilio($this->invited_mobile_phone);
		}
	}

    /**
     * @return string
     */
    public function routeNotificationForTwilio()
	{
		return prepareMobileForTwilio($this->invited_mobile_phone);
	}

    /**
     * @param array $options
     * @return bool|void
     */
    public function save(array $options = [])
	{
		if (!$this->invitation_token && !$this->id){
			$this->invitation_token = Str::random(40);
		}

		parent::save($options);
	}

    /**
     * @return array
     */
    public function transform()
    {
        return [
            'id' => $this->id,
            'email' => $this->invited_email,
        ];
    }


}