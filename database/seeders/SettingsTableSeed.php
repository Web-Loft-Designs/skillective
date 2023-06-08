<?php

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsTableSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Throwable
     */
    public function run() {
		DB::table('system_settings')->truncate();

		$item_data = [
			'name' => 'sitename',
			'value' => 'Skillective'
		];
		Setting::create($item_data);

		$item_data = [
			'name' => 'contact_form_recepients',
			'value' => 'skillective.test@yahoo.com'
		];
		Setting::create($item_data);


		$item_data = [
			'name' => 'copyright',
			'value' => '© %Y% Skillective.com All rights reserved.'
		];
		Setting::create($item_data);

		$item_data = [
			'name' => 'booking_confirmation_text',
			'value' => '<h3>Your Lesson is Reserved!</h3>
                <p><strong>Payment for this lesson will be charged once the instructor has approved this booking request.</strong></p>
                <ul>
                    <li>
                        <span class="img-circle"><img src="/images/icon-dialog.png" alt=""></span>
                        <p>Details for the Lesson can now be found in the <a href="/student/dashboard">upcoming lessons</a> section in your Skillective User profile</p>
                    </li>
                    <li>
                        <span class="img-circle"><img src="/images/icon-clock.png" alt=""></span>
                        <p>Skillective recommends arriving 10-15 minutes early to ensure ample time for parking and or locating your instructor</p>
                    </li>
                    <li>
                        <span class="img-circle"><img src="/images/icon-calendar-inner.png" alt=""></span>
                        <p>Accept the calendar invite sent to your e-mail to add this lesson to your calendar</p>
                    </li>
                </ul>
                <a href="/lessons" class="btn-secondary btn-block">Find more lessons</a>'
		];
		Setting::create($item_data);

		$item_data = [
			'name' => 'default_profile_image',
			'value' => '/uploads/default_profile_image.png'
		];
		Setting::create($item_data);
		\File::copy(base_path('public/www/images/default_profile_image.png'),base_path('public/uploads/default_profile_image.png'));

		$item_data = [
			'name' => 'default_genre_image',
			'value' => '/uploads/default_genre_image.jpg'
		];
		Setting::create($item_data);
		\File::copy(base_path('public/www/images/default_genre_image.jpg'),base_path('public/uploads/default_genre_image.jpg'));

		$item_data = [
			'name' => 'header-menu',
			'value' => 'a:2:{i:0;a:2:{s:10:"link_title";s:4:"Help";s:8:"link_url";s:5:"/help";}i:1;a:2:{s:10:"link_title";s:17:"About Skillective";s:8:"link_url";s:9:"/about-us";}}'
		];
		Setting::create($item_data);

		$item_data = [
			'name' => 'footer-menu',
			'value' => 'a:3:{i:0;a:2:{s:10:"link_title";s:8:"Site Map";s:8:"link_url";s:8:"/sitemap";}i:1;a:2:{s:10:"link_title";s:5:"Terms";s:8:"link_url";s:6:"/terms";}i:2;a:2:{s:10:"link_title";s:14:"Privacy Policy";s:8:"link_url";s:15:"/privacy-policy";}}'
		];
		Setting::create($item_data);

		$item_data = [
			'name' => 'social-menu',
			'value' => 'a:3:{i:0;a:2:{s:10:"link_title";s:7:"Twitter";s:8:"link_url";s:19:"https://twitter.com";}i:1;a:2:{s:10:"link_title";s:8:"Facebook";s:8:"link_url";s:20:"https://facebook.com";}i:2;a:2:{s:10:"link_title";s:9:"Instagram";s:8:"link_url";s:21:"https://instagram.com";}}'
		];
		Setting::create($item_data);

		$item_data = [
			'name' => 'max_allowed_instructor_invites',
			'value' => 25
		];
		Setting::create($item_data);

		$item_data = [
			'name' => 'max_allowed_student_invites',
			'value' => 9999
		];
		Setting::create($item_data);

		$item_data = [
			'name' => 'ga_tracking_code',
			'value' => ''
		];
		Setting::create($item_data);

		$item_data = [
			'name' => 'time_to_approve_booking', // in hours before it will be automatically cancelled
			'value' => 48
		];
		Setting::create($item_data);

		$item_data = [
			'name' => 'instructor_registration_confirmation_text',
			'value' => 'Your request is being reviewed by Skillective. You will be notified via email to complete profile once request approved.'
		];
		Setting::create($item_data);

		$item_data = [
			'name' => 'onboard_as_merchant',
			'value' => 'You have to onboard as a merchant to sell your lessons.'
		];
		Setting::create($item_data);

		$item_data = [
			'name' => 'merchant_account_on_review',
			'value' => 'Your merchant account is being verified. This message will disappear once it is approved and then funds may be disbursed to your account'
		];
		Setting::create($item_data);

		$item_data = [
			'name' => 'merchant_account_declined',
			'value' => 'Your merchant account has been declined.'
		];
		Setting::create($item_data);

		$item_data = [
			'name' => 'skillective_service_fee',
			'value' => 1
		];
		Setting::create($item_data);

		$item_data = [
			'name' => 'favicon',
			'value' => '/uploads/favicon.png'
		];
		Setting::create($item_data);

		$item_data = [
			'name' => 'free_instructor_registration_enabled',
			'value' => 0
		];
		Setting::create($item_data);

		$item_data = [
			'name' => 'report_count_payment_form_views',
			'value' => 0
		];
		Setting::create($item_data);

		$item_data = [
			'name' => 'report_total_searches',
			'value' => 0
		];
		Setting::create($item_data);

		$item_data = [
			'name' => 'report_count_searches_by_genre',
			'value' => 0
		];
		Setting::create($item_data);

		$item_data = [
			'name' => 'report_count_searches_by_location',
			'value' => 0
		];
		Setting::create($item_data);
	}
}
