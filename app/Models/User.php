<?php

namespace App\Models;

use Braintree\MerchantAccount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Log;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;

class User extends Authenticatable implements HasMedia, Transformable
{

	use Notifiable, HasRoles, SoftDeletes, InteractsWithMedia;

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
		'first_name', 'last_name', 'email', 'password', 'accepted_invitation_id', 'tax_id', 'legal_name','pp_tracking_id','pp_merchant_id','pp_referral_id','pp_account_status'
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
     * @return Builder
     */
    public function scopeSearchFromNameInstagram(Builder $query, string $searchString): Builder
    {
        return $query->where(static function (Builder $subQuery) use ($searchString) {
            $subQuery->where('first_name', 'LIKE', "%{$searchString}%")
                ->orWhere('last_name', 'LIKE', "%{$searchString}%")
                ->orWhereHas('profile', static function (Builder $relationQuery) use ($searchString) {
                    $relationQuery->where('instagram_handle', 'LIKE', "%{$searchString}%");
                });
            });
    }

    /**
     * @return string
     */
    public function routeNotificationForTwilio()
	{
		return prepareMobileForTwilio($this->profile->mobile_phone);
	}


    /**
     * @return BelongsToMany
     */
    public function genres()
	{
		return $this->belongsToMany(Genre::class, 'user_genre')
            ->orderBy('title')
            ->where('is_disabled', 0);
	}


    /**
     * @return HasMany
     */
    public function regularNotifications()
	{
		return $this->hasMany(RegularNotification::class, 'user_regular_notifications');
	}

    /**
     * @return BelongsToMany
     */
    public function clients()
	{
		return $this->belongsToMany(User::class, 'instructor_client', 'instructor_id', 'client_id')->withTimestamps();
	}


    /**
     * @return BelongsToMany
     */
    public function instructors()
	{
		return $this->belongsToMany(
            User::class,
            'student_instructor',
            'student_id',
            'instructor_id'
        )->withTimestamps()
            ->withPivot('is_favorite', 'geo_notifications_allowed', 'virtual_notifications_allowed');
	}


    /**
     * @return HasOne
     */
    public function profile()
	{
		return $this->hasOne(Profile::class);
	}


    /**
     * @return HasMany
     */
    public function social()
	{
		return $this->hasMany(Social::class);
	}

    /**
     * @return HasMany
     */
    public function instructorInvitations()
	{
		return $this->hasMany(Invitation::class, 'invited_by')
            ->where('invited_as_instructor', 1);
	}

    /**
     * @return HasMany
     */
    public function studentInvitations()
	{
		return $this->hasMany(Invitation::class, 'invited_by')
            ->where('invited_as_instructor', 0);
	}


    /**
     * @return HasMany
     */
    public function lessons()
	{
		return $this->hasMany(Lesson::class, 'instructor_id', 'id');
	}

    /**
     * @return HasMany
     */
    public function discounts()
	{
		return $this->hasMany(Discount::class, 'instructor_id', 'id');
	}

    /**
     * @return HasMany
     */
    public function bookings()
	{
		return $this->hasMany(Booking::class, 'student_id', 'id');
	}

    /**
     * @return HasMany
     */
    public function myLessonsBookings()
	{
		return $this->hasMany(Booking::class, 'instructor_id', 'id');
	}

    /**
     * @return HasMany
     */
    public function purchasedLessons()
	{
		return $this->hasMany(PurchasedLesson::class, 'student_id', 'id');
	}

    /**
     * @return HasMany
     */
    public function myPreLessonsPurchases()
	{
		return $this->hasMany(PurchasedLesson::class, 'instructor_id', 'id');
	}

    /**
     * @return HasMany
     */
    public function paymentMethods()
	{
		return $this->hasMany(UserPaymentMethod::class, 'user_id', 'id');
	}

    /**
     * @return HasMany
     */
    public function geoLocations()
	{
		return $this->hasMany(UserGeoLocation::class);
	}

    /**
     * @return string
     */
    public function getName()
	{
		return $this->first_name . ' ' . $this->last_name;
	}

    /**
     * @return array
     */
    public function transform()
	{
		return [
			'id' => $this->id,
			'first_name' => $this->first_name,
			'last_name' => $this->last_name,
			'full_name' => $this->getName(),
			'email' => $this->getEmail(),
			'profile' => !empty($this->profile) ? $this->profile->transform(): [],
			'genres' => $this->genres->toArray(),
			'is_featured' => $this->isFeatured(),
			'discounts' => $this->discounts->toArray()
		];
	}

    /**
     * @return array
     */
    public function toArray()
	{
		return [
			'id' => $this->id,
			'first_name' => $this->first_name,
			'last_name' => $this->last_name,
			'full_name' => $this->getName(),
			'email' => $this->getEmail(),
			'profile' => $this->profile->transform(),
			'genres' => $this->genres->toArray(),
			'discounts' => $this->discounts->toArray()
		];
	}

    /**
     * @param $password
     * @return void
     */
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

    /**
     * @param $password
     * @return string
     */
    public static function encodePassword($password)
	{
		return Hash::make($password);
	}

    /**
     * @param $password
     * @return bool
     */
    public function checkCurrentPassword($password)
	{
		return Hash::check($password, $this->password);
	}

    /**
     * @return mixed|string
     */
    public function getEmail()
	{
		return $this->hasFakeEmail() ? '' : $this->email;
	}

    /**
     * @return bool
     */
    public function hasFakeEmail()
	{
		return strpos($this->email, getFakeEmailBase()) > 0;
	}

    /**
     * @return HasOne
     */
    public function isFeatured()
	{
		return $this->hasOne(FeaturedInstructor::class, 'instructor_id');
	}


    /**
     * @param $status
     * @return void
     */
    public function setStatus($status)
	{
		if (in_array($status, self::getStatuses())) {
			$this->status = $status;
			$this->save();
			$this->fireModelEvent('statusChanged');
		}
	}


    /**
     * @return string[]
     */
    public static function getStatuses()
	{
		return [
			self::STATUS_ON_REVIEW,
			self::STATUS_APPROVED,
			self::STATUS_BLOCKED,
			self::STATUS_ACTIVE
		];
	}

    /**
     * @param $str
     * @return void
     */
    public function setFinishRegistrationToken($str)
	{
		if ($str != '') {
			$this->finish_registration_token = Hash::make($str);
		} else {
			$this->finish_registration_token = '';
		}
		$this->save();
	}

    /**
     * @param Media|null $media
     * @return void
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null): void
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

    /**
     * @param $instructorId
     * @return mixed
     */
    public function hasOwnInstructor($instructorId)
	{
		return ($this->instructors->contains($instructorId)); //  $this->instructors()->find($instructorId)!=null
	}

    /**
     * @param $instructorId
     * @return bool
     */
    public function hasOwnFavoriteInstructor($instructorId)
	{
		return (($ownInstructor = $this->instructors()->find($instructorId)) != null && $ownInstructor->pivot->is_favorite == 1);
	}

    /**
     * @param $requestData
     * @param $collectionName
     * @return int
     */
    public function uploadMedia($requestData, $collectionName = 'website_images')
	{
		$countUploadedMedia = 0;
		try {
			foreach ($requestData as $paramName => $paramValue) {
				if ($paramName == 'media') {
					if ($paramValue instanceof UploadedFile) {
						$this->addMedia($paramValue)->toMediaCollection($collectionName);
						$countUploadedMedia++;
					} else { // array of medias
						for ($i = 0; $i < count($paramValue); $i++) {
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

    /**
     * @param $mediaUrl
     * @param $countLikes
     * @param $countComments
     * @return void
     * @throws FileCannotBeAdded
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function addInstagramMedia($mediaUrl, $countLikes = 0, $countComments = 0)
	{
		$collectionName = 'instagram';
		$this->addMediaFromUrl($mediaUrl)
			->withCustomProperties(['count_likes' => $countLikes, 'count_comments' => $countComments])
			->toMediaCollection($collectionName);
	}

    /**
     * @param $limit
     * @return array
     */
    public function getGalleryMedia($limit = null)
	{
		$media = [];
		$mediaCollection = $this->media()
			->whereIn('collection_name', ['website_images', 'instagram'])
			->orderBy('id', 'DESC');
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

    /**
     * @return true
     */
    public function suspend()
	{
		$this->setStatus(self::STATUS_BLOCKED);
		return true;
	}

    /**
     * @return void
     */
    public function resetBraintreeData()
	{
		$this->bt_submerchant_id = null;
		$this->bt_submerchant_status = null;
		$this->bt_submerchant_status_reason = null;
		$this->save();
	}

    /**
     * @param MerchantAccount $merchantAccountId
     * @return void
     */
    public function setBraintreeData(MerchantAccount $merchantAccountId)
	{
		$this->bt_submerchant_id = $merchantAccountId->id;
		$this->bt_submerchant_status = $merchantAccountId->status;
		$this->bt_submerchant_status_reason = null;
		$this->save();
	}

    /**
     * @param $status
     * @param $message
     * @return void
     */
    public function updateUserSubMerchantStatus($status, $message = '')
	{
		$prevStatus = $this->bt_submerchant_status;
		$this->bt_submerchant_status = $status;
		$this->bt_submerchant_status_reason = $message;
		$this->save();
		if ($prevStatus != $status)
			$this->fireModelEvent('submerchantStatusChanged');
	}

    /**
     * @return int
     */
    public function getMaxAllowedInstructorInvites()
	{
		$personalMaxAllowedInstructorInvites = $this->profile->max_allowed_instructor_invites;
		if ($personalMaxAllowedInstructorInvites == null)
			return (int)Setting::getValue('max_allowed_instructor_invites');
		else
			return $personalMaxAllowedInstructorInvites;
	}

    /**
     * @return bool
     */
    public function canAddNewLesson()
	{
        // перевірка чи може інструктор створити урок
		return (
           ( ($this->bt_submerchant_id != null && $this->bt_submerchant_status == 'active') || ($this->pp_merchant_id != null) )
			&& ($this->hasMedia('website_images') || $this->hasMedia('instagram'))
			&& ($this->profile->avatar != '' && $this->profile->avatar != null)
        );
	}
}
