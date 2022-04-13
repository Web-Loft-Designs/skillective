<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;
use Spatie\Image\Manipulations;
use Prettus\Repository\Contracts\Transformable;

class User extends Authenticatable implements HasMedia, Transformable
{
	use Notifiable, HasRoles, SoftDeletes, HasMediaTrait;

	const STATUS_ON_REVIEW	= 'on_review';
	const STATUS_APPROVED	= 'approved';
	const STATUS_BLOCKED	= 'blocked';
	const STATUS_ACTIVE		= 'active';

	const ROLE_INSTRUCTOR	= 'Instructor';
	const ROLE_STUDENT		= 'Student';
	const ROLE_ADMIN		= 'Admin';

	const ALLOWED_SM_PROVIDERS = [
		'ig',
		'facebook',
		'twitter',
		'snapchat'
	];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'first_name', 'last_name', 'email', 'password', 'accepted_invitation_id'
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
	];

	/**
	 * Additional observable events.
	 */
	protected $observables = [
		'statusChanged',
		'submerchantStatusChanged'
	];

    /**
     * @param Builder $query
     * @param string $searchString
     *
     * @return Builder
     */
    public function scopeSearchFromNameInstagram(Builder $query, string $searchString): Builder
    {
        return $query->where(static function (Builder $subQuery) use ($searchString) {
            $subQuery->where('first_name', 'LIKE', "{$searchString}%")
                ->orWhere('last_name', 'LIKE', "{$searchString}%")
                ->orWhereHas('profile', static function (Builder $relationQuery) use ($searchString) {
                    $relationQuery->where('instagram_handle', 'LIKE', "{$searchString}%");
                });
            });
    }

	public function routeNotificationForTwilio()
	{
		return prepareMobileForTwilio($this->profile->mobile_phone);
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 **/
	public function genres()
	{
		return $this->belongsToMany(\App\Models\Genre::class, 'user_genre')->orderBy('title')->where('is_disabled', 0);
	}


	public function regularNotifications()
	{
		return $this->hasMany(\App\Models\RegularNotification::class, 'user_regular_notifications');
	}
	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 **/
	public function clients()
	{
		return $this->belongsToMany(\App\Models\User::class, 'instructor_client', 'instructor_id', 'client_id')->withTimestamps();
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 **/
	public function instructors()
	{
		return $this->belongsToMany(\App\Models\User::class, 'student_instructor', 'student_id', 'instructor_id')->withTimestamps()->withPivot('is_favorite', 'geo_notifications_allowed', 'virtual_notifications_allowed');
	}

	/**
	 * User Profile Relationships.
	 *
	 * @var array
	 */
	public function profile()
	{
		return $this->hasOne(\App\Models\Profile::class);
	}

	/**
	 * Build Social Relationships.
	 *
	 * @var array
	 */
	public function social()
	{
		return $this->hasMany('App\Models\Social');
	}

	public function instructorInvitations()
	{
		return $this->hasMany('App\Models\Invitation', 'invited_by')->where('invited_as_instructor', 1);
	}

	public function studentInvitations()
	{
		return $this->hasMany('App\Models\Invitation', 'invited_by')->where('invited_as_instructor', 0);
	}

	/**
	 * Build Lesson Relationships.
	 *
	 * @var array
	 */
	public function lessons()
	{
		return $this->hasMany('App\Models\Lesson', 'instructor_id', 'id');
	}

	public function discounts()
	{
		return $this->hasMany('App\Models\Discount', 'instructor_id', 'id');
	}

	public function bookings()
	{
		return $this->hasMany('App\Models\Booking', 'student_id', 'id');
	}

	public function myLessonsBookings()
	{
		return $this->hasMany('App\Models\Booking', 'instructor_id', 'id');
	}

	public function purchasedLessons()
	{
		return $this->hasMany('App\Models\PurchasedLesson', 'student_id', 'id');
	}

	public function myPreLessonsPurchases()
	{
		return $this->hasMany('App\Models\PurchasedLesson', 'instructor_id', 'id');
	}

	// student saved payment methods
	public function paymentMethods()
	{
		return $this->hasMany('App\Models\UserPaymentMethod', 'user_id', 'id');
	}

	public function geoLocations()
	{
		return $this->hasMany('App\Models\UserGeoLocation');
	}

	public function getName()
	{
		return $this->first_name . ' ' . $this->last_name;
	}

	public function transform()
	{
		return [
			'id' => $this->id,
			'first_name' => $this->first_name,
			'last_name' => $this->last_name,
			'full_name' => $this->getName(),
			'email' => $this->getEmail(),
			'profile' => $this->profile->transform(),
			//			'isInstructor' => $this->hasRole(self::ROLE_INSTRUCTOR),
			//			'isStudent' => $this->hasRole(self::ROLE_STUDENT),
			'genres' => $this->genres->toArray(),
			'is_featured' => $this->isFeatured(),
			'discounts' => $this->discounts->toArray()
		];
	}

	public function toArray()
	{
		return [
			'id' => $this->id,
			'first_name' => $this->first_name,
			'last_name' => $this->last_name,
			'full_name' => $this->getName(),
			'email' => $this->getEmail(),
			'profile' => $this->profile->transform(),
			//			'isInstructor' => $this->hasRole(self::ROLE_INSTRUCTOR),
			//			'isStudent' => $this->hasRole(self::ROLE_STUDENT),
			'genres' => $this->genres->toArray(),
			'discounts' => $this->discounts->toArray()
		];
	}

	public function changePassword($password)
	{
		if (strpos($password, 'skillectivefake-') === 0) {
			$this->attributes['password'] = 'skillectivefake-' . self::encodePassword($password);
			$this->save();
		} else {
			$this->attributes['password'] = self::encodePassword($password);
			$this->save();
		}
	}

	public static function encodePassword($password)
	{

		return Hash::make($password);
	}

	public function checkCurrentPassword($password)
	{
		return Hash::check($password, $this->password);
	}

	public function getEmail()
	{
		return $this->hasFakeEmail() ? '' : $this->email;
	}

	public function hasFakeEmail()
	{
		return strpos($this->email, getFakeEmailBase()) > 0;
	}

	public function isFeatured()
	{
		return $this->hasOne(\App\Models\FeaturedInstructor::class, 'instructor_id');
	}

	public function setStatus($status)
	{
		if (in_array($status, self::getStatuses())) {
			$this->status = $status;
			$this->save();
			$this->fireModelEvent('statusChanged');
		}
	}

	public static function getStatuses()
	{
		return [
			self::STATUS_ON_REVIEW,
			self::STATUS_APPROVED,
			self::STATUS_BLOCKED,
			self::STATUS_ACTIVE
		];
	}

	public function setFinishRegistrationToken($str)
	{
		if ($str != '') {
			$this->finish_registration_token = Hash::make($str);
		} else {
			$this->finish_registration_token = '';
		}
		$this->save();
	}

	public function registerMediaConversions(Media $media = null)
	{
		$this->addMediaConversion('thumb')
			->fit(Manipulations::FIT_CROP, 200, 200)
			->optimize()
			->nonQueued()
			->performOnCollections('website_images', 'instagram');

		$this->addMediaConversion('thumb_overview')
			->fit(Manipulations::FIT_CROP, 40, 40)
			->nonQueued()
			->performOnCollections('website_images', 'instagram');
	}

	public function hasOwnInstructor($instructorId)
	{
		return ($this->instructors->contains($instructorId)); //  $this->instructors()->find($instructorId)!=null
	}

	public function hasOwnFavoriteInstructor($instructorId)
	{
		return (($ownInstructor = $this->instructors()->find($instructorId)) != null && $ownInstructor->pivot->is_favorite == 1);
	}


	public function uploadMedia($requestData, $collectionName = 'website_images')
	{
		$countUploadedMedia = 0;
		try {
			foreach ($requestData as $paramName => $paramValue) {
				if ($paramName == 'media') {
					if ($paramValue instanceof \Illuminate\Http\UploadedFile) {
						//						$mime = $paramValue->getClientMimeType();
						//						$collectionName = strpos($mime, 'video') === false ? 'images' : 'videos';
						$this->addMedia($paramValue)->toMediaCollection($collectionName);
						$countUploadedMedia++;
					} else { // array of medias
						for ($i = 0; $i < count($paramValue); $i++) {
							//							$mime = $paramValue[$i]->getClientMimeType();
							//							$collectionName = strpos($mime, 'video') === false ? 'images' : 'videos';
							$this->addMedia($paramValue[$i])->toMediaCollection($collectionName);
							$countUploadedMedia++;
						}
					}
				}
			}
		} catch (\Exception $e) {
			Log::error($e->getMessage());
		}
		return $countUploadedMedia;
	}

	public function addInstagramMedia($mediaUrl, $countLikes = 0, $countComments = 0)
	{
		$collectionName = 'instagram';
		$this->addMediaFromUrl($mediaUrl)
			->withCustomProperties(['count_likes' => $countLikes, 'count_comments' => $countComments])
			->toMediaCollection($collectionName);
	}

	public function getGalleryMedia($limit = null)
	{ // \Illuminate\Support\Carbon $uploadedAfter = null,
		$media = [];
		$mediaCollection = $this->media()
			->whereIn('collection_name', ['website_images', 'instagram'])
			->orderBy('id', 'DESC');

		//		if ($uploadedAfter!=null){
		//			$mediaCollection->where('created_at', '>=', $uploadedAfter->format('Y-m-d H:i:s'));
		//		}
		if ($limit) {
			$mediaCollection->limit($limit);
		}

		$mediaCollection->get()
			->each(function ($item, $key) use (&$media) {
				$media[] = [
					'id'		=> $item->id,
					'model_id'		=> $item->model_id,
					'url'		=> $item->getFullUrl(),
					'thumb_url'	=> $item->getFullUrl('thumb'),
					'collection_name' => $item->collection_name,
					'count_likes' => $item->hasCustomProperty('count_likes') ? $item->getCustomProperty('count_likes') : 0,
					'count_comments' => $item->hasCustomProperty('count_comments') ? $item->getCustomProperty('count_comments') : 0
				];
			});

		return $media;
	}

	public function suspend()
	{
		$this->setStatus(self::STATUS_BLOCKED);
		return true;
	}

	public function resetBraintreeData()
	{
		$this->bt_submerchant_id = null;
		$this->bt_submerchant_status = null;
		$this->bt_submerchant_status_reason = null;
		$this->save();
	}

	public function setBraintreeData(\Braintree_MerchantAccount $merchantAccountId)
	{
		$this->bt_submerchant_id = $merchantAccountId->id;
		$this->bt_submerchant_status = $merchantAccountId->status;
		$this->bt_submerchant_status_reason = null;
		$this->save();
	}

	public function updateUserSubMerchantStatus($status, $message = '')
	{
		$prevStatus = $this->bt_submerchant_status;
		$this->bt_submerchant_status = $status;
		$this->bt_submerchant_status_reason = $message;
		$this->save();
		if ($prevStatus != $status)
			$this->fireModelEvent('submerchantStatusChanged');
	}

	public function getMaxAllowedInstructorInvites()
	{
		$personalMaxAllowedInstructorInvites = $this->profile->max_allowed_instructor_invites;
		if ($personalMaxAllowedInstructorInvites == null)
			return (int)Setting::getValue('max_allowed_instructor_invites');
		else
			return $personalMaxAllowedInstructorInvites;
	}

	public function canAddNewLesson()
	{
		return ($this->bt_submerchant_id != null
			&& $this->bt_submerchant_status == 'active'
			&& ($this->hasMedia('website_images') || $this->hasMedia('instagram'))
			&& ($this->profile->avatar != '' && $this->profile->avatar != null));
	}
}
