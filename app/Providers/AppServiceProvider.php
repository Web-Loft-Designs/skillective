<?php

namespace App\Providers;

use Braintree\Configuration;
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
use DateTime;
use DateTimeZone;
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
		Configuration::environment(config('services.braintree.environment'));
		Configuration::merchantId(config('services.braintree.merchant_id'));
		Configuration::publicKey(config('services.braintree.public_key'));
		Configuration::privateKey(config('services.braintree.private_key'));

		$this->bootIgSocialite();

		Validator::extend('valid_us_state', function ($attribute, $value, $parameters) {
			return in_array($value, collect(getUSStates())->pluck('code')->toArray());
		});

		Validator::extend('valid_limit', function ($attribute, $value, $parameters) {
			return in_array($value, array_keys(UserGeoLocation::getAvailableLimits()));
		});

		Validator::extend('is_exact_address', function ($attribute, $value, $parameters) {
			$locationDetails = getLocationDetails($value);

			return ($locationDetails['timezone_id'] != null && in_array($locationDetails['location_type'], ['ROOFTOP', 'RANGE_INTERPOLATED', 'GEOMETRIC_CENTER', 'APPROXIMATE'])); // require exact location
		});

		Validator::extend('is_approximate_address', function ($attribute, $value, $parameters) {
			$locationDetails = getLocationDetails($value);
			return ($locationDetails['city'] != null && $locationDetails['state'] != null && $locationDetails['timezone_id'] != null && in_array($locationDetails['location_type'], ['ROOFTOP', 'RANGE_INTERPOLATED', 'GEOMETRIC_CENTER', 'APPROXIMATE'])); // require exact location
		});

		Validator::extend('is_real_city_in_us', function ($attribute, $value, $parameters) {
			$state = $parameters[0];
			$locationDetails = getLocationDetails("$value $state, USA");
			return ($locationDetails['city'] != null && $state == $locationDetails['state']); // require the city is defined
		});

		Validator::extend('validate_min_profile_price', function ($attribute, $value, $parameters) {

			$instructorId = $parameters[0];
			$start = \Carbon\Carbon::parse($parameters[1]);
			$end = \Carbon\Carbon::parse($parameters[2]);
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

        Validator::extend('virtual_min_price', function ($attribute, $value, $parameters)
        {
            $lessonType = request('lesson_type');

            if ($lessonType == 'virtual') {
                $minPrice = (float) data_get(auth()->user(), 'profile.virtual_min_price');
                if (empty($minPrice) && $value < 1) return false;
                else if (!empty($minPrice) && $value < $minPrice) return false;
            } else if ($value < 1) {
                return false;
            }

            return true;
        });

		Validator::extend('max_words', function ($attribute, $value, $parameters) {
			$countWords = str_word_count($value);
			return ($countWords <= $parameters[0]);
		});

		Validator::extend('valid_timezone', function ($attribute, $value, $parameters) {
			if($parameters[0] == 'in_person'){
				return true;
			}

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

        Validator::extend('future_date', static function (string $attribute, string $value, array $parameters) {
            $request = Request::capture();

            if (
                !$request->time_to ||
                !$request->time_from ||
                strtotime('today ' . $request->time_to) === false ||
                strtotime('today ' . $request->time_from) === false) {
                return true; // time validation will prevent passing validation in this case
            }

            if ($request->lesson_type === 'in_person_client') {
                return true;
            }

            if ($request->location) {
                $lessonLocationDetails = getLocationDetails($request->location);
                if ($lessonLocationDetails['timezone_id']) {
                    if (strtotime($value . ' ' . $request->time_from) > strtotime(Carbon::now($lessonLocationDetails['timezone_id'])->format('Y-m-d H:i:s'))) {
                        return true;
                    }

                    return false;
                }

                return true; // location validation will prevent passing validation in this case
            }

            if (!$request->timezone_id) {
                return true; // location validation will prevent passing validation in this case
            }

            if (strtotime($value . ' ' . $request->time_from) > strtotime(Carbon::now($request->timezone_id)->format('Y-m-d H:i:s'))) {
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

        Validator::extend('no_lessons_this_time', static function (string $attribute, string $date, array $parameters) {
            $request = Request::capture();

            $timeFrom = $request->input($parameters[0]);
            $timeTo = $request->input($parameters[1]);
            $timeZone = $request->input($parameters[5]);

            if (!$timeTo || !$timeFrom) {
                return true;
            }

            if(!$timeZone) {
                $lessonLocationDetails = getLocationDetails($request->location);
                if ($lessonLocationDetails['timezone_id']) {
                    $timeZone = $lessonLocationDetails['timezone_id'];
                } else {
                    $timeZone = config('geoip.default_location.timezone');
                }
            }

            $dateTimeZone = new DateTimeZone($timeZone);

            $startDateTime = DateTime::createFromFormat(
                'Y-m-d H:i:s',
                "{$request->input($parameters[3])} {$timeFrom}",
                $dateTimeZone
            );

            $endDateTime = DateTime::createFromFormat(
                'Y-m-d H:i:s',
                "{$request->input($parameters[4])} {$timeTo}",
                $dateTimeZone
            );

            if (!$startDateTime || !$endDateTime) {
                return false;
            }

            $startDateTimeString = $startDateTime->format('Y-m-d H:i:s');
            $endDateTimeString = $endDateTime->format('Y-m-d H:i:s');

            $userLessons = Auth::user()->lessons()
                ->whereRaw("(lessons.is_cancelled is NULL OR lessons.is_cancelled=0)")
                ->whereRaw("(
                    (start >= CONVERT_TZ('{$startDateTimeString}', '{$timeZone}', timezone_id) AND start < CONVERT_TZ('{$endDateTimeString}', '{$timeZone}', timezone_id)) OR
                    (end > CONVERT_TZ('{$startDateTimeString}', '{$timeZone}', timezone_id) AND end <= CONVERT_TZ('{$endDateTimeString}', '{$timeZone}', timezone_id)) OR
                    (start < CONVERT_TZ('{$startDateTimeString}', '{$timeZone}', timezone_id) AND end > CONVERT_TZ('{$endDateTimeString}', '{$timeZone}', timezone_id))
                )");

            $lesson_id = $request->input($parameters[2]);
            if ($lesson_id) {
                $userLessons->where('id', '!=', $lesson_id);
            }

            return $userLessons->count() === 0;
        });

		User::observe(UserObserver::class);
		Booking::observe(BookingObserver::class);
		Lesson::observe(LessonObserver::class);
		LessonRequest::observe(LessonRequestObserver::class);
	}

	private function bootIgSocialite(): void
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
