<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Log;

class UserGeoLocation extends Model
{
    use SoftDeletes;

    public $table = 'user_geo_locations';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'user_id',
        'city',
        'state',
        'limit',
		'lat',
		'lng',
		'location',
		'date_from',
		'date_to',
		'timezone_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'city' => 'string',
        'state' => 'string',
        'limit' => 'integer',
		'lat' => 'float',
		'lng' => 'float',
		'timezone_id' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
//		'city' => ['required', 'string', 'max:255'],
//		'state' => ['required', 'valid_us_state'],
		'location' => ['required'],
		'limit' => ['required', 'integer'],
		'date_from' => ['required'],
		'date_to' => ['required'],
    ];

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

	public static function getAvailableLimits(){
		return [
			'10'						=> '+10 Miles',
			'50'						=> '+50 Miles',
			'100'						=> '+100 Miles'
		];
	}

	public static function getDefaultLimit(){
		return 50;
	}

	public function save(array $options = [])
	{
		$locationDetails = getLocationDetails($this->location);

		$this->lat = isset($locationDetails['lat']) ? $locationDetails['lat'] : null;
		$this->lng = isset($locationDetails['lng']) ? $locationDetails['lng'] : null;
		$this->city = isset($locationDetails['city']) ? $locationDetails['city'] : null;
		$this->state = isset($locationDetails['state']) ? $locationDetails['state'] : null;
		$this->timezone_id = isset($locationDetails['timezone_id']) ? $locationDetails['timezone_id'] : null;

		parent::save($options);
	}
}