<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\Models\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],
	'twilio' => [
		'username' => env('TWILIO_USERNAME'), // optional when using auth token
		'password' => env('TWILIO_PASSWORD'), // optional when using auth token
		'auth_token' => env('TWILIO_AUTH_TOKEN'), // optional when using username and password
		'account_sid' => env('TWILIO_ACCOUNT_SID'),
		'from' => env('TWILIO_FROM'), // optional
        'api_sid' => env('TWILIO_API_SID'),
        'api_secret' => env('TWILIO_API_SECRET'),
//        'video_small_group_minute_price' => env('TWILIO_SMALL_GROUP_VIDEO_ROOM_PRICE'),
//        'video_group_minute_price' => env('TWILIO_GROUP_VIDEO_ROOM_PRICE'),
	],
	'instagram' => [
		'client_id' => env('INSTAGRAM_KEY'),
		'client_secret' => env('INSTAGRAM_SECRET'),
		'redirect' => env('INSTAGRAM_REDIRECT_URI')
	],
	'ig' => [
		'client_id' => env('IG_APP_ID'),
		'client_secret' => env('IG_APP_SECRET'),
		'redirect' => env('IG_REDIRECT_URI')
	],
	'facebook' => [
		'client_id'     => env('FB_ID'),
		'client_secret' => env('FB_SECRET'),
		'redirect'      => env('FB_REDIRECT'),
	],
	'snapchat' => [
		'client_id'     => env('SNAPCHAT_ID'),
		'client_secret' => env('SNAPCHAT_SECRET'),
		'redirect'      => env('SNAPCHAT_REDIRECT'),
	],
	'whatsapp' => [
		'from' => env('WHATSAPP_FROM')
	],
	'braintree' => [
		'environment' => env('BRAINTREE_ENV'),
		'merchant_id' => env('BRAINTREE_MERCHANT_ID'),
		'public_key'  => env('BRAINTREE_PUBLIC_KEY'),
		'private_key' => env('BRAINTREE_PRIVATE_KEY'),
		'master_merchant_account_id' => env('BRAINTREE_MASTER_MERCHANT_ACCOUNT_ID'),
//		'processing_percent_fee' => env('BRAINTREE_PROCESSING_PERCENT_FEE'),
//		'transaction_fixed_fee' => env('BRAINTREE_TRANSACTION_FIXED_FEE'),
	],
	'google' => [
		'client_id'     => env('GOOGLE_ID'),
		'client_secret' => env('GOOGLE_SECRET'),
		'redirect'      => env('GOOGLE_REDIRECT'),
		'api_key'		=> env('GOOGLE_API_KEY_PUBLIC'),
		'api_key_private'	=> env('GOOGLE_API_KEY_PRIVATE')
	],
];
