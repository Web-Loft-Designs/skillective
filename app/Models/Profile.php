<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Jobs\LoadInstagramMediaJob;
use Prettus\Repository\Contracts\Transformable;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class Profile extends Model implements Transformable
{
    use SoftDeletes;

    public $table = 'profiles';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

	const IMAGES_PATH = 'profiles/';

    protected $dates = ['deleted_at', 'dob'];

    public $fillable = [
        'user_id',
        'instagram_handle',
        'address',
        'city',
        'state',
        'zip',
        'mobile_phone',
        'dob',
        'about_me',
        'avatar',
		'notification_methods',
		'gender',
        'lat',
        'lng',
        'lesson_block_min_price', // for instructors
        'virtual_min_price',
    ];


    /**
     * @var string[]
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'instagram_handle' => 'string',
        'address' => 'string',
        'city' => 'string',
        'state' => 'string',
        'zip' => 'string',
        'dob' => 'date',
        'about_me' => 'string',
        'avatar' => 'string',
		'notification_methods' => 'array',
		'instagram_followers_count' => 'integer',
		'gender' => 'string',
        'lat' => 'float',
        'lng' => 'float',
        'lesson_block_min_price' => 'float',
        'virtual_min_price' => 'float',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'user_id' => 'required'
    ];

	public function __construct(array $attributes = [])
	{
		if (!isset($attributes['notification_methods']))
			$attributes['notification_methods'] = ['email'];
		parent::__construct($attributes);
	}


    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return array
     */
    public function transform()
	{
		return [
			'id' => $this->id,
			'user_id' => $this->user_id,
			'instagram_handle' => trim($this->instagram_handle, '@'),
			'address' => $this->address,
			'city' => $this->city,
			'state' => $this->state,
			'zip' => $this->zip,
			'full_address' => $this->getFullAddress(),
			'mobile_phone' => presentMobilePhone($this->mobile_phone),
			'dob' => $this->dob ? $this->dob->toDateString(): null,
			'about_me' => $this->about_me,
			'image' => $this->getImageUrl(),
			'notification_methods' => $this->notification_methods,
			'gender' => $this->gender,
			'max_allowed_instructor_invites' => $this->max_allowed_instructor_invites,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'lesson_block_min_price' => $this->lesson_block_min_price==null ? 50 : $this->lesson_block_min_price,
            'virtual_min_price' => $this->virtual_min_price
		];
	}

    /**
     * @return string[]
     */
    public static function getAvailableNotificationMethods(){
		return [
			'email'		=> 'Email',
			'sms'		=> 'Text Message',
		];
	}

    /**
     * @return string
     */
    public function getFullAddress(){
		$fullAddress = trim(
			str_replace('  ', ' ', implode(' ', [$this->address, $this->city, $this->state, $this->zip])),
			' '
		);
		return $fullAddress;
	}

    /**
     * @return string
     */
    public function getImageUrl(){

      //TODO  test 'https://skillective.com'
        if(config('app.env') == 'local') {
            if ($this->avatar) {
                return 'https://skillective.com' . '/storage/' . self::IMAGES_PATH . $this->id . '/' . $this->avatar;
            } else {
                return 'https://skillective.com' . Setting::getValue('default_profile_image');
            }
        } else {
            if ($this->avatar) {
                return config('app.url') . '/storage/' . self::IMAGES_PATH . $this->id . '/' . $this->avatar;
            } else {
                return config('app.url') . Setting::getValue('default_profile_image');
            }
        }
	}

    /**
     * @param $url
     * @return mixed
     */
    public function downloadImageFromUrl($url){
		$this->deleteOldImage();

		$destination = storage_path('app/public/' . self::IMAGES_PATH .$this->id.'/');
		if (! is_file($destination)) {
			File::makeDirectory($destination, 0775, true, true);
		}
		$path_parts	= pathinfo(strtok($url, '?'));
		$baseName	= $path_parts['basename'];
		$ext		= isset($path_parts['extension']) ? $path_parts['extension'] : 'jpg';
		$fileName	= $path_parts['filename'];

		$index = 1;
		$baseName = $fileName . '.' . $ext;
		while (File::exists($destination.$baseName)){
			$baseName = $fileName . '-'.$index . '.' . $ext;
			$index++;
		}
		$img = Image::make($url);
		$img->resize(200, 200, function ($constraint) {
			$constraint->aspectRatio();
		});
		$img->resizeCanvas(200, 200, 'center', false, array(255, 255, 255, 0));
		$img->stream();

		Storage::disk('public')->put(self::IMAGES_PATH.$this->id.'/'.$baseName, $img);
		$this->update(['avatar'=>$baseName]);

		return $fileName;
	}

    /**
     * @param $profile_image
     * @return string
     */
    public function updateProfileImage($profile_image){
		$filename = 'profile-image.'.$profile_image->getClientOriginalExtension();
		$save_path = storage_path('app/public/' . self::IMAGES_PATH .$this->id.'/');
		$public_path = self::IMAGES_PATH.$this->id.'/'.$filename;

		// revove the old image
		if ($this->avatar != null && is_file($save_path.basename($this->avatar))) {
			unlink($save_path.basename($this->avatar));
		}

		// Make the user a folder and set permissions
		File::makeDirectory($save_path, $mode = 0755, true, true);

		// Save the file to the server
		$img = Image::make($profile_image)->resize(200, 200, function ($constraint) {
			$constraint->aspectRatio();
		});

		$img->resizeCanvas(200, 200, 'center', false, array(255, 255, 255, 0));
		$img->save($save_path.$filename);

			// Save the public image path
		$this->avatar = $filename;//$public_path;
		$this->save();

		return $this->getImageUrl();
	}

    /**
     * @return bool
     */
    public function deleteOldImage(){
		$destination = storage_path('app/public/' . self::IMAGES_PATH .$this->id.'/');
		// remove the old image
		if ($this->avatar!= '' && File::exists($destination.$this->avatar)) {
			unlink($destination.$this->avatar);
			$this->update(['avatar'=>null]);
			return true;
		}
		return false;
	}

    /**
     * @param $instagramUserObject
     * @return void
     */
    public function updateProfileWithInstagramData($instagramUserObject){
		$this->instagram_handle			= $instagramUserObject->user['username'];
		$this->instagram_token			= $instagramUserObject->token;
		$this->save();

		if ($this->user->hasRole(User::ROLE_INSTRUCTOR)){
			LoadInstagramMediaJob::dispatch( $this->user_id )->delay(now()->addSecond());
		}

	}

    /**
     * @param $facebookUserObject
     * @return void
     */
    public function updateProfileWithFacebookData($facebookUserObject){
		$this->downloadImageFromUrl($facebookUserObject->avatar);
	}

    /**
     * @param $val
     * @return void
     */
    public function setMobilePhoneAttribute($val){
		$this->attributes['mobile_phone'] = preg_replace('/[\.\+\s\(\)]/', '', $val);
	}

    /**
     * @param $val
     * @return string
     */
    public function getInstagramHandleAttribute($val){
		return trim($val, '@');
	}

    /**
     * @param $val
     * @return int
     */
    public function getGoalValueAttribute($val){
		return $val==null ? 0 : $val;
	}

    /**
     * @param $val
     * @return string
     */
    public function getGoalColorAttribute($val){
		return $val==null ? '#AAAAAA' : $val;
	}

    /**
     * @return array
     */
    public function getGoal(){
		return [
			'goal_value' => $this->goal_value,
			'goal_description' => $this->goal_description,
			'goal_color' => $this->goal_color
		];
	}

    /**
     * @param array $options
     * @return bool|void
     */
    public function save(array $options = [])
    {
		$locationDetails = getLocationDetails( "{$this->city}, {$this->state}" );

        $this->lat = isset($locationDetails['lat']) ? $locationDetails['lat'] : null;
        $this->lng = isset($locationDetails['lng']) ? $locationDetails['lng'] : null;

		Log::info($this);
        parent::save($options);
    }
}
