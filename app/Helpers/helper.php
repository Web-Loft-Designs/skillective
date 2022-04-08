<?php

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

function getUsTimezones()
{

	$zones = [];
	$timezone_identifiers = DateTimeZone::listIdentifiers();

	for ($i = 0; $i < count($timezone_identifiers); $i++) {
		$zones[$timezone_identifiers[$i]] = $timezone_identifiers[$i];
	}

	return $zones;
}

function getTimezoneAbbrev($timezone_id)
{
	$zones = [
		'America/New_York' => 'EST',
		'America/Chicago' => 'CST',
		'America/Denver' => 'MST',
		'America/Phoenix' => 'MST',
		'America/Los_Angeles' => 'PST',
		'America/Anchorage' => 'AST',
		'America/Adak' => 'HST',
		'Pacific/Honolulu' => 'HST',
	];

	return isset($zones[$timezone_id]) ? $zones[$timezone_id] : $timezone_id;
}


function getStatesAssociativeArray()
{
	$stateOptions = [
		'AL' => 'Alabama',
		'AK' => 'Alaska',
		'AZ' => 'Arizona',
		'AR' => 'Arkansas',
		'CA' => 'California',
		'CO' => 'Colorado',
		'CT' => 'Connecticut',
		'DE' => 'Delaware',
		'DC' => 'District Of Columbia',
		'FL' => 'Florida',
		'GA' => 'Georgia',
		'HI' => 'Hawaii',
		'ID' => 'Idaho',
		'IL' => 'Illinois',
		'IN' => 'Indiana',
		'IA' => 'Iowa',
		'KS' => 'Kansas',
		'KY' => 'Kentucky',
		'LA' => 'Louisiana',
		'ME' => 'Maine',
		'MD' => 'Maryland',
		'MA' => 'Massachusetts',
		'MI' => 'Michigan',
		'MN' => 'Minnesota',
		'MS' => 'Mississippi',
		'MO' => 'Missouri',
		'MT' => 'Montana',
		'NE' => 'Nebraska',
		'NV' => 'Nevada',
		'NH' => 'New Hampshire',
		'NJ' => 'New Jersey',
		'NM' => 'New Mexico',
		'NY' => 'New York',
		'NC' => 'North Carolina',
		'ND' => 'North Dakota',
		'OH' => 'Ohio',
		'OK' => 'Oklahoma',
		'OR' => 'Oregon',
		'PA' => 'Pennsylvania',
		'RI' => 'Rhode Island',
		'SC' => 'South Carolina',
		'SD' => 'South Dakota',
		'TN' => 'Tennessee',
		'TX' => 'Texas',
		'UT' => 'Utah',
		'VT' => 'Vermont',
		'VA' => 'Virginia',
		'WA' => 'Washington',
		'WV' => 'West Virginia',
		'WI' => 'Wisconsin',
		'WY' => 'Wyoming',

		'AS' => 'American Samoa',
		'DC' => 'District of Columbia',
		'FM' => 'Federated States of Micronesia',
		'GU' => 'Guam',
		'MH' => 'Marshall Islands',
		'MP' => 'Northern Mariana Islands',
		'PW' => 'Palau',
		'PR' => 'Puerto Rico',
		'VI' => 'Virgin Islands',
		//		'AE' => 'Armed Forces Africa',
		//		'AA' => 'Armed Forces Americas',
		//		'AE' => 'Armed Forces Canada',
		//		'AE' => 'Armed Forces Europe',
		//		'AE' => 'Armed Forces Middle East',
		//		'AP' => 'Armed Forces Pacific',
	];

	ksort($stateOptions);

	return $stateOptions;
}

function getUSStates()
{
	$stateOptions = getStatesAssociativeArray();

	//    foreach ($stateOptions as $k=>$v)
	//        $stateOptions[$k] = ucwords(strtolower($v));

	$states = [];
	foreach ($stateOptions as $code => $name) {
		$states[] = ['code' => $code, 'name' => ucwords(strtolower($name))];
	}

	return $states;
}

function getStateKeyByName($stateKey)
{
	$states = buildStateOptions();
	$stateKey = ucwords(strtolower($stateKey));
	$key = array_search($stateKey, $states);
	return $key;
}

function getRoutePerPage($request)
{
	$perPageName = 'per_page_' . str_replace('/', '_', $request->path());
	return Session::get($perPageName, 10);
}

function captureRoutePerPage($request)
{
	if (($perPage = (int)$request->input('l', 0)) > 0) {
		$perPageName = 'per_page_' . str_replace('/', '_', $request->path());
		Session::put($perPageName, $perPage);
	}
}

function getRolesOptionsList($exclude_ids = [])
{
	$roles = Role::orderBy('name', 'ASC');
	if (count($exclude_ids))
		$roles->whereNotIn('id', $exclude_ids);
	$roles = $roles->get();
	$data = ['' => 'All roles'];
	foreach ($roles as $role) {
		$data[$role->id] = $role->name;
	}
	asort($data);

	return $data;
}

function getFirstName($name)
{
	$parts = explode(' ', $name);
	if (count($parts) > 1)
		return implode(' ', array_slice($parts, 0, -1));
	return $name;
}

function getLastName($name)
{
	$parts = explode(' ', $name);
	if (count($parts) > 1)
		return array_slice(explode(' ', $name), -1)[0];
	return '';
}

function getFakeEmailBase()
{
	return '@fake.' . preg_replace('/https?:\/\//', '', config('app.url'));
}

function escape_like($value, $char = '\\')
{
	return addslashes(str_replace(
		[$char, '%', '_'],
		[$char . $char, $char . '%', $char . '_'],
		$value
	));
}

function currentUserCanBook()
{
	return (!Auth::user() || Auth::user()->hasRole(\App\Models\User::ROLE_STUDENT));
}

// validation rules

function getDOBValidationRules()
{
	return ['required', 'date_format:Y-m-d', 'before:16 years ago'];
}
function getDOBValidationMessages()
{
	return [
		'dob.required'			=> "Date of birth required",
		'dob.date_format'		=> "Not valid date format",
		'dob.before'			=> "If you are under 16, please have a legal guardian to create an account"
	];
}

function getCityValidationRules($request)
{
	return ['required', 'string', 'max:255', 'is_real_city_in_us:' . $request->input('state', '')];
}
function getCityValidationMessages()
{
	return [
		'city.is_real_city_in_us' => "Wrong City name or City State"
	];
}

function getAboutMeValidationRules()
{
	return ['sometimes', 'string', "max_words:300"];
}

function getMobilePhoneValidationRules()
{
	return ['required', 'regex:/^((\+1)|(\+37))\s\([0-9]{3}\)\s[0-9]{3}\s[0-9]{4}$/'];
}

function getFundingMobilePhoneValidationRules()
{
	return ['sometimes', 'nullable', 'regex:/^(\+1)\s\([0-9]{3}\)\s[0-9]{3}\s[0-9]{4}$/'];
}

function getSSNValidationRules()
{
	return ['sometimes', 'nullable', 'regex:/^[0-9]{3}\-[0-9]{2}\-[0-9]{4}$/'];
}

function getInviteMobilePhoneValidationRules()
{
	return ['required', 'regex:/^\+1[0-9]{10}$/'];
}

function getPasswordValidationFormat()
{
	return '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[\d#?!@$%^&*-]).{8,}$/';
}

function getPasswordValidationFormatError()
{
	return "Passwords must be at least 8 characters long. The password must contain at least three character categories among the following: Uppercase characters (A-Z), Lowercase characters (a-z), Special character or digit";
}

function getPostCodeValidationRules()
{
	return ['required', 'regex:/^[0-9]{5}(\-[0-9]{4})?$/'];
}

function getAboutMeValidationMessages()
{
	return [
		'about_me.max_words'	=> "Max count words is 300"
	];
}
function getGenders()
{
	return ['male', 'female'];
}

function getInviteMobilePhoneValidationRegexp()
{
	return '\+1[0-9]{10}';
}

function getInviteEmailValidationRegexp()
{
	return '([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}';
}

// <<< validation rules

// static pages data

function isDashboardPage()
{
	$r = request();
	return ($r->is('instructor/dashboard')
		|| $r->is('instructor/schedule')
		|| $r->is('instructor/bookings')
		|| $r->is('instructor/clients')
		|| $r->is('instructor/gallery')
		|| $r->is('instructor/invite-instructor')
		|| $r->is('instructor/incomes')
		|| $r->is('instructor/payouts')
		|| $r->is('student/gallery')
		|| $r->is('student/dashboard')
		|| $r->is('student/schedule')
		|| $r->is('student/bookings')
		|| $r->is('student/instructors')
		|| $r->is('profile/edit'));
}

function getDashboardUrl()
{
	if (Auth::user()->hasRole('Instructor'))
		return route('instructor.dashboard');
	elseif (Auth::user()->hasRole('Student'))
		return route('student.dashboard');
	elseif (Auth::user()->hasRole('Admin'))
		return route('backend.dashboard');
	return '/';
}

function getCurrentPage($slug = null)
{
	if ($slug == null) {
		$requestUri = request()->getRequestUri();
		$requestUriParts = explode('?', $requestUri, 2);
		$slug = $requestUriParts[0] != '/' ? trim($requestUriParts[0], '/') : $requestUriParts[0]; //segment(count(request()->segments()));
	}
	$page = App\Models\Page::where('name', '=', $slug)->with('meta')->first();
	return $page;
}

function getCurrentPageMetaValue($page, $name)
{
	$value = null;
	if ($page instanceof \App\Models\Page and $page->meta) {
		foreach ($page->meta as $meta) {
			if ($name == $meta->name) {
				$value = $meta->value;
				break;
			}
		}
	}
	return $value;
}
// <<< static pages data

// google api
function getCoordinatesByAddress($address)
{
	$latitude = null;
	$longitude = null;
	if ($address) {
		$key = config('services.google.api_key_private');
		$url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&sensor=false&key=$key";
		$client = new GuzzleHttp\Client();
		$google_api_response = $client->request('GET', $url);

		$responseData = json_decode($google_api_response->getBody()->getContents());
		if (isset($responseData->status) && $responseData->status == 'OK') {
			$latitude = $responseData->results[0]->geometry->location->lat;
			$longitude = $responseData->results[0]->geometry->location->lng;
		}
	}

	return array(
		'lat'  => $latitude,
		'lng' => $longitude
	);
}

function getLocationDetails($address)
{

	$addressComponents = [];
	if ($address) {
		$key = config('services.google.api_key_private');
		$url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&key=$key";
		$client = new GuzzleHttp\Client();
		$google_api_response = $client->request('GET', $url);
		$responseData = json_decode($google_api_response->getBody()->getContents());
		//		dd($responseData);



		if (isset($responseData->status) && $responseData->status == 'OK') {
			for ($i = 0; $i < count($responseData->results[0]->address_components); $i++) {
				$addressComponents[$responseData->results[0]->address_components[$i]->types[0]] = $responseData->results[0]->address_components[$i];
			}
			$addressComponents['lat'] = $responseData->results[0]->geometry->location->lat;
			$addressComponents['lng'] = $responseData->results[0]->geometry->location->lng;
			$addressComponents['location_type'] = $responseData->results[0]->geometry->location_type;
			// administrative_area_level_1.short_name = state ; locality.long_name = city ; country.short_name = US
		}
	}

	$timezone = null;
	if (isset($addressComponents['lat']) && ($addressComponents['lng'])) {
		$timezone = getTimezoneByCoordinates($addressComponents['lat'], $addressComponents['lng']);
	}

	$details = [
		'state'  => isset($addressComponents['administrative_area_level_1']) ? $addressComponents['administrative_area_level_1']->short_name : null,
		'city' => isset($addressComponents['locality']) ? $addressComponents['locality']->long_name : null,
		'address' => (isset($addressComponents['street_number']) ? $addressComponents['street_number']->long_name : '') . ' ' . (isset($addressComponents['route']) ? $addressComponents['route']->long_name : '') . ' ' . (isset($addressComponents['sublocality_level_1']) ? $addressComponents['sublocality_level_1']->long_name : ''),
		'zip'	=> isset($addressComponents['postal_code']) ? $addressComponents['postal_code']->long_name : null,
		'lat'	=> isset($addressComponents['lat']) ? $addressComponents['lat'] : null,
		'lng'	=> isset($addressComponents['lng']) ? $addressComponents['lng'] : null,
		'timezone_id' => ($timezone != null) ? $timezone->timeZoneId : null,
		'location_type' => isset($addressComponents['location_type']) ? $addressComponents['location_type'] : null,
	];

	return $details;
}

function getTimezoneByCoordinates($lat, $lng)
{
	$timezoneData = null;
	if ($lat && $lng) {
		$key = config('services.google.api_key_private');
		$now = time();
		$url = "https://maps.googleapis.com/maps/api/timezone/json?location=" . urlencode("$lat,$lng") . "&timestamp=$now&key=$key";
		$client = new GuzzleHttp\Client();
		$google_api_response = $client->request('GET', $url);

		$responseData = json_decode($google_api_response->getBody()->getContents());
		if (isset($responseData->status) && $responseData->status == 'OK') {
			$timezoneData = $responseData; // dstOffset, rawOffset, timeZoneId, timeZoneName
			//            $timezoneRawOffset = $responseData->rawOffset;
		}
	}
	return $timezoneData;
}


function googleMapsAddressLink($address)
{
	return "<a href='http://maps.google.com/maps?q=" . urlencode($address) . "' target='_blank'>On Map</a>";
}

// <<< google api

function presentMobilePhone($mobilePhone)
{
	$mobilePhoneLength = strlen($mobilePhone);
	$outputPhone = '+' . substr($mobilePhone, -$mobilePhoneLength, -10) . ' (' . substr($mobilePhone, -10, -7) . ')' . ' ' . substr($mobilePhone, -7, -4) . ' ' . substr($mobilePhone, -4);
	return $outputPhone;
}

function prepareMobileForTwilio($mobilePhone)
{
	return '+' . preg_replace('/[\+\s\(\)]/', '', $mobilePhone);
}

function getPercentDiff($current, $prev)
{
	$diff = 0;
	if ($prev == 0)
		$diff = 100;
	elseif ($prev != 0)
		$diff = ($current - $prev) / $prev * 100;

	return number_format($diff, 1, '.', '');
}

function updateSearchReports($request)
{
	$settingsModel = app()->make('App\Models\Setting');
	$settingsModel->incrementValue('report_total_searches');
	if ($request->filled('genre'))
		$settingsModel->incrementValue('report_count_searches_by_genre');
	if ($request->filled('location'))
		$settingsModel->incrementValue('report_count_searches_by_location');
}

function getYoutubeId($youtube_url)
{
	$pattern = '/^.*(?:(?:youtu\.be\/|v\/|vi\/|u\/\w\/|embed\/)|(?:(?:watch)?\?v(?:i)?=|\&v(?:i)?=))([^#\&\?]*).*/';
	if (
		$youtube_url &&
		filter_var($youtube_url, FILTER_VALIDATE_URL) &&
		preg_match($pattern, $youtube_url, $matches)
	) {
		return $matches[1];
	}

	return '';
}
