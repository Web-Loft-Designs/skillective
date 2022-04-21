<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Two\FacebookProvider;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Booking;
use App\Models\Lesson;
use App\Models\LessonRequest;
use App\Models\UserGeoLocation;
use App\Observers\UserObserver;
use App\Observers\BookingObserver;
use App\Observers\LessonObserver;
use App\Observers\LessonRequestObserver;
use App\Repositories\UserRepository;
use App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot(UserRepository $userRepository)
	{
		\Braintree_Configuration::environment(config('services.braintree.environment'));
		\Braintree_Configuration::merchantId(config('services.braintree.merchant_id'));
		\Braintree_Configuration::publicKey(config('services.braintree.public_key'));
		\Braintree_Configuration::privateKey(config('services.braintree.private_key'));

		$this->bootIgSocialite();

		Validator::extend('valid_us_state', function ($attribute, $value, $parameters) {
			return in_array($value, collect(getUSStates())->pluck('code')->toArray());
		});

		Validator::extend('valid_limit', function ($attribute, $value, $parameters) {
			return in_array($value, array_keys(UserGeoLocation::getAvailableLimits()));
		});

		Validator::extend('is_exact_address', function ($attribute, $value, $parameters) {
			$locationDetails = getLocationDetails($value);
            $timezoneId = data_get($locationDetails, 'timezone_id');
            $locationType = data_get($locationDetails, 'location_type');
			return ($timezoneId != null && in_array($locationType, ['ROOFTOP', 'RANGE_INTERPOLATED', 'GEOMETRIC_CENTER', 'APPROXIMATE']));
		});

		Validator::extend('is_approximate_address', function ($attribute, $value, $parameters) {
			$locationDetails = getLocationDetails($value);
			return ($locationDetails['city'] != null && $locationDetails['state'] != null && $locationDetails['timezone_id'] != null && in_array($locationDetails['location_type'], ['ROOFTOP', 'RANGE_INTERPOLATED', 'GEOMETRIC_CENTER', 'APPROXIMATE'])); // require exact location
		});

		Validator::extend('is_real_city_in_us', function ($attribute, $value, $parameters) {
			$state = $parameters[0];
			//            \Log::info("$value $state, USA");
			$locationDetails = getLocationDetails("$value $state, USA");
			return ($locationDetails['city'] != null && $state == $locationDetails['state']); // require the city is defined
		});

		Validator::extend('validate_min_profile_price', function ($attribute, $value, $parameters) {

			$instructorId = $parameters[0];
			$start = Carbon::parse($parameters[1]);
			$end = Carbon::parse($parameters[2]);
			Log::info($instructorId);
			Log::info($start);
			Log::info($end);

			$instructor = User::find($instructorId);
			if ($instructor == null) {
				return false;
			}

			$minPrice = (float)$instructor->profile->lesson_block_min_price;

			if (!$minPrice) {
				$minPrice = 50;
			}

			$countSpots = ceil($end->diffInMinutes($start) / 30);

			Log::info($countSpots);
			Log::info($minPrice);

			return (strtotime($parameters[1]) !== false && strtotime($parameters[2]) !== false && $minPrice > 0) ? ($minPrice * $countSpots <= $value) : true;
		});

		Validator::extend('max_words', function ($attribute, $value, $parameters) {
			$countWords = str_word_count($value);
			return ($countWords <= $parameters[0]);
		});

		Validator::extend('valid_timezone', function ($attribute, $value, $parameters) {
            if($parameters[0] == 'in_person') return true;
			return in_array($value, \DateTimeZone::listIdentifiers());
		});

		Validator::extend('is_admin_email', function ($attribute, $value, $parameters) {
			$user = User::where('email', $value)->first();
			if ($user)
				return $user->hasRole('Admin');
			return false;
		});

		Validator::extend('not_admin_email', function ($attribute, $value, $parameters) {
			$user = User::where('email', $value)->first();
			if ($user)
				return !$user->hasRole('Admin');
			return false;
		});

		Validator::extend('email_active', function ($attribute, $value, $parameters) {
			$user = User::where('email', $value)->first();
			if ($user)
				return $user->status == 'active';
			return false;
		});

		Validator::extend('hash_match', function ($attribute, $value, $parameters) {
			$user = Auth::user();
			return ($user != null && $user->checkCurrentPassword($value));
		});

		Validator::extend('future_date', function ($attribute, $value) {
            $request = request();
            $timeFrom = data_get($request, 'time_from');
            $timeTo = data_get($request, 'time_to');
            $lessonType = data_get($request, 'lesson_type');
            $location = data_get($request, 'location');
            $timezoneId = data_get($request, 'timezone_id');

            // time validation will prevent passing validation in this case
            if ((!$timeFrom || !$timeTo) || ($timeFrom == 'Invalid date' || $timeTo == 'Invalid date')) return true;

			if($lessonType == 'in_person_client') return true;

            if (!empty($location) && empty($timezoneId)) {
				$lessonLocationDetails = getLocationDetails($location);
				if ($lessonLocationDetails['timezone_id']) {
					if (strtotime($value . ' ' . $timeFrom) > strtotime(\Carbon\Carbon::now($lessonLocationDetails['timezone_id'])->format('Y-m-d H:i:s'))) {
						return true;
					}
				} else {
					return true; // location validation will prevent passing validation in this case
				}
			} else if (empty($location) && !empty($timezoneId)) {
				if (strtotime($value . ' ' . $timeFrom) > strtotime(\Carbon\Carbon::now($timezoneId)->format('Y-m-d H:i:s'))) {
					return true;
				}
			} else if(empty($location) && empty($timezoneId)) {
                return true;
            }

			return false;
		});

		Validator::extend('date_multi_format', function ($attribute, $value, $formats) {
			// iterate through all formats
			foreach ($formats as $format) {
				$parsed = date_parse_from_format($format, $value);
				if ($parsed['error_count'] === 0 && $parsed['warning_count'] === 0) {
					return true;
				}
			}
			return false;
		});

		Validator::extend('no_lessons_this_time', function ($attribute, $date) {
			$request = request();
			$timeFrom	= data_get($request, 'time_from');
			$timeTo = data_get($request, 'time_to');
            $lessonId = (int) data_get($request, 'lesson_id');
            $timezone = data_get($request, 'timezone_id');

            if ((!$timeFrom || !$timeTo) || ($timeFrom == 'Invalid date' || $timeTo == 'Invalid date')) return true;

            $start	= Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $timeFrom);
            $end = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $timeTo);

			if($timezone) $start->setTimezone($timezone);
            if($timezone) $end->setTimezone($timezone);

			if ($start && $end) {
				$start = $start->format('Y-m-d H:i:s');
				$end = $end->format('Y-m-d H:i:s');

				$userLessons = Auth::user()->lessons()
					->whereRaw(" ( lessons.is_cancelled is NULL OR lessons.is_cancelled=0 ) ")
					->whereRaw("DATE(start) = '" . $start . "'")
					->whereRaw("(
                            (start >= '" . $start . "' AND start < '" . $end . "')
                            OR (end > '" . $start . "' AND end <= '" . $end . "')
                            OR (start < '" . $start . "' AND end > '" . $end . "')
                        )");

				$userLessons->whereNull('deleted_at');

				if ($lessonId) $userLessons->where('id', '!=', $lessonId);

				return ($userLessons->count() == 0);
			} else if(!$timeFrom || $timeTo) {
                return true;
            }

			return false;
		});

		User::observe(UserObserver::class);
		Booking::observe(BookingObserver::class);
		Lesson::observe(LessonObserver::class);
		LessonRequest::observe(LessonRequestObserver::class);
	}

	private function bootIgSocialite()
	{
		$r = request();
		$socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
		$socialite->extend(
			'ig',
			function ($app) use ($socialite, $r) {
				$config = $app['config']['services.ig'];
				if ($r->is('social/ig/instructor/registration')) {
					$config['redirect'] = route('social.instructor.registration', ['provider' => 'ig']);
				}
				return $socialite->buildProvider(\App\Providers\Socialite\IgProvider::class, $config);
			}
		);
		$socialite->extend(
			'facebook',
			function ($app) use ($socialite, $r) {
				$config = $app['config']['services.facebook'];
				if ($r->is('social/facebook/instructor/registration')) {
					$config['redirect'] = route('social.instructor.registration', ['provider' => 'facebook']);
				}
				return $socialite->buildProvider(FacebookProvider::class, $config);
			}
		);
		$socialite->extend(
			'snapchat',
			function ($app) use ($socialite, $r) {
				$config = $app['config']['services.snapchat'];
				if ($r->is('social/snapchat/instructor/registration')) {
					$config['redirect'] = route('social.instructor.registration', ['provider' => 'snapchat']);
				}
				return $socialite->buildProvider(App\Providers\Socialite\SnapchatProvider::class, $config);
			}
		);
	}
}
