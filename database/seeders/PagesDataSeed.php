<?php

use Illuminate\Database\Seeder;
use App\Models\Page;
use App\Models\PageMeta;

class PagesDataSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Throwable
     */
    public function run()
    {
        $faker = Faker\Factory::create('en_US');

        /*
        DB::table('page_meta')->truncate();
        DB::table('pages')->truncate();

        $pages = [
            [
                'name'      => '/',
                'title'     => 'Home',
                'content'   => '',
                'meta'      => [
                    [
                        'name' => 'text_above_filter',
                        'value' => 'Learn The Skills You Want From Influencers & Instructors Near You'
                    ],
                    [
                        'name' => '_image_filter_block_image',
                        'value' => '/uploads/home-banner.jpg'
                    ],
					[
						'name' => 'filter_form_title',
						'value' => 'Find and Book Lessons'
					],
					[
						'name' => 'how_it_works_title',
						'value' => 'How It Works'
					],
					[
						'name' => 'how_it_works_text',
						'value' => 'Book your favorite Social Media Influencers or Instructors near you through the Skillective Platform. See when their availble, where their available and lesson pricing in real time'
					],
					[
						'name' => 'benefits_title',
						'value' => 'Why Skillective'
					],
					[
						'name' => 'benefits',
						'value' => 'a:6:{i:0;a:2:{s:5:"title";s:13:"Benefit title";s:4:"text";s:180:"Book your favorite Social Media Influencers or Instructors near you through the Skillective Platform. See when their availble, where their available and lesson pricing in real time";}i:1;a:2:{s:5:"title";s:13:"Benefit title";s:4:"text";s:180:"Book your favorite Social Media Influencers or Instructors near you through the Skillective Platform. See when their availble, where their available and lesson pricing in real time";}i:2;a:2:{s:5:"title";s:13:"Benefit title";s:4:"text";s:180:"Book your favorite Social Media Influencers or Instructors near you through the Skillective Platform. See when their availble, where their available and lesson pricing in real time";}i:3;a:2:{s:5:"title";s:13:"Benefit title";s:4:"text";s:180:"Book your favorite Social Media Influencers or Instructors near you through the Skillective Platform. See when their availble, where their available and lesson pricing in real time";}i:4;a:2:{s:5:"title";s:13:"Benefit title";s:4:"text";s:180:"Book your favorite Social Media Influencers or Instructors near you through the Skillective Platform. See when their availble, where their available and lesson pricing in real time";}i:5;a:2:{s:5:"title";s:13:"Benefit title";s:4:"text";s:180:"Book your favorite Social Media Influencers or Instructors near you through the Skillective Platform. See when their availble, where their available and lesson pricing in real time";}}'
					],
                ]
            ],

            [
                'name'      => 'help',
                'title'     => 'Help',
                'content'   => '<p>'.$faker->text().'</p>',
				'template'	=> 'help',
				'meta'      => [
					[
						'name' => 'form_sent_confirmation',
						'value' => 'Message has been sent! We will contact you as soon as possible.'
					],
				]
            ],

            [
                'name'      => 'privacy-policy',
                'title'     => 'Privacy policy',
				'content'   => '<p>'.$faker->text().'</p>',
            ],

			[
				'name'      => 'terms',
				'title'     => 'Terms of Use',
				'content'   => '<p>'.$faker->text().'</p>',
			],

			[
				'name'      => 'sitemap',
				'title'     => 'Sitemap',
				'content'   => '<p>'.$faker->text().'</p>',
			],

			[
				'name'      => 'about-us',
				'title'     => 'About Skillective',
				'content'   => '<p></p>',
				'template'	=> 'about-us',
				'meta'      => [
					[
						'name' => 'about_us_block_title',
						'value' => 'Learn the Skills you want from Instructors and Influencers near you'
					],
					[
						'name' => 'about_us_block_text',
						'value' => '<ul>
	<li><img alt="" src="/uploads/icon-qw.png" />Have you ever wanted to work with someone, but you didn&rsquo;t know how to contact then or book them?</li>
	<li><img alt="" src="/uploads/icon-qw.png" />Have you ever wondered if your favorite Influencer or teacher will be traveling through your town?</li>
	<li><img alt="" src="/uploads/icon-qw.png" />Do you find yourself looking through endless videos or pictures on Instagram watching the Skills you desire?</li>
</ul>
<h3>Now you can find who you want to lean from</h3>
<p>and have a platform that notifies you when they are close to you and avaialable for lessons. Search a broad array of genres for the Skills you have always wanted to learn.</p>'
					],
					[
						'name' => '_image_about_us_block_bg',
						'value' => '/uploads/about-1.jpg'
					],
					[
						'name' => 'booking_steps_block_title',
						'value' => 'Time is your most Valuable Asset'
					],
					[
						'name' => 'booking_steps',
						'value' => 'a:4:{i:0;a:3:{s:5:"title";s:22:"Student searches Genre";s:19:"_current_image_step";s:24:"/uploads/search-icon.svg";s:11:"_image_step";s:24:"/uploads/search-icon.svg";}i:1;a:2:{s:5:"title";s:22:"Views Media & Profiles";s:11:"_image_step";s:23:"/uploads/piplu-icon.svg";}i:2;a:2:{s:5:"title";s:27:"Selects Instructor and Time";s:11:"_image_step";s:30:"/uploads/calendar-big-icon.svg";}i:3;a:2:{s:5:"title";s:12:"Books Lesson";s:11:"_image_step";s:20:"/uploads/ok-icon.svg";}}'
					],
					[
						'name' => '_image_our_experience_block_bg',
						'value' => '/uploads/about-2.jpg'
					],
					[
						'name' => 'our_experience_block_text',
						'value' => '<p><strong>With Almost 20 years of coaching and Pricate lesson experience, we have decided to build the better mouse trap. A tool that is easy to use for instructors and simple for Students to navigate and book.</strong></p>

<p>The booking and scheduling process is long and cumbersome; if you don&rsquo;t put in the time your lessons begin to dwindle as schedules conflict and time management becomes more challenging.</p>'
					],
					[
						'name' => 'start_now_block_text',
						'value' => '<p>We have all gotten busier so let skillective be you personal assitant in helping get your private lessons and clinics set schedules and booked.</p>
<a class="btn" href="/lessons">Start Now!</a>'
					],
					[
						'name' => '_image_start_now_block_bg',
						'value' => '/uploads/call-to-action.jpg'
					]
				]
			],
			[
				'name'      => 'instructor/register',
				'title'     => 'Become an Instructor',
				'content'   => '',
				'meta'      => [
					[
						'name' => 'form_block_welcome',
						'value' => 'Become an Instructor and Start Earning Today!'
					],
					[
						'name' => 'form_benefits',
						'value' => 'a:3:{i:0;a:2:{s:5:"title";s:80:"The best platform for sports instructors, more than <strong>3000 joined</strong>";s:14:"_image_benefit";s:23:"/uploads/traner-img.png";}i:1;a:2:{s:5:"title";s:84:"<strong>Deep Instagram integration</strong>, the largest clients base in your region";s:14:"_image_benefit";s:23:"/uploads/insta-icon.png";}i:2;a:2:{s:5:"title";s:65:"<strong>$5632</strong> average earnings instructor on Skillective";s:14:"_image_benefit";s:23:"/uploads/money-icon.png";}}'
					],
					[
						'name' => 'testimonial_block_name',
						'value' => 'Sarah Laurentis'
					],
					[
						'name' => 'testimonial_block_position',
						'value' => 'Yoga & Pilates instructor (5 years experience)'
					],
					[
						'name' => 'testimonial_block_text',
						'value' => 'After I began to use Skillective as my main planning tool for training, my income doubled and the number of clients continues to grow rapidly. Thank you soooo much guys :)'
					],
					[
						'name' => 'benefits_title',
						'value' => 'More benefits'
					],
					[
						'name' => '_image_form_block_bg',
						'value' => '/uploads/register-bg.jpg'
					],
					[
						'name' => '_image_testimonial_block_bg',
						'value' => '/uploads/single-testimonial.jpg'
					],
					[
						'name' => 'benefits',
						'value' => 'a:6:{i:0;a:2:{s:5:"title";s:13:"Benefit title";s:4:"text";s:180:"Book your favorite Social Media Influencers or Instructors near you through the Skillective Platform. See when their availble, where their available and lesson pricing in real time";}i:1;a:2:{s:5:"title";s:13:"Benefit title";s:4:"text";s:180:"Book your favorite Social Media Influencers or Instructors near you through the Skillective Platform. See when their availble, where their available and lesson pricing in real time";}i:2;a:2:{s:5:"title";s:13:"Benefit title";s:4:"text";s:180:"Book your favorite Social Media Influencers or Instructors near you through the Skillective Platform. See when their availble, where their available and lesson pricing in real time";}i:3;a:2:{s:5:"title";s:13:"Benefit title";s:4:"text";s:180:"Book your favorite Social Media Influencers or Instructors near you through the Skillective Platform. See when their availble, where their available and lesson pricing in real time";}i:4;a:2:{s:5:"title";s:13:"Benefit title";s:4:"text";s:180:"Book your favorite Social Media Influencers or Instructors near you through the Skillective Platform. See when their availble, where their available and lesson pricing in real time";}i:5;a:2:{s:5:"title";s:13:"Benefit title";s:4:"text";s:180:"Book your favorite Social Media Influencers or Instructors near you through the Skillective Platform. See when their availble, where their available and lesson pricing in real time";}}'
					],
				]
			],
			[
				'name'      => 'student/register',
				'title'     => 'Sign up',
				'content'   => '',
				'meta'      => [
					[
						'name' => 'form_block_welcome',
						'value' => 'Find & book favorite instructors around the world'
					],
					[
						'name' => 'form_block_text',
						'value' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
					],
					[
						'name' => 'form_benefits',
						'value' => 'a:3:{i:0;a:2:{s:5:"title";s:63:"The best platform for sports instructors, more than 3000 joined";s:14:"_image_benefit";s:25:"/uploads/traner-img-1.png";}i:1;a:2:{s:5:"title";s:67:"Deep Instagram integration, the largest clients base in your region";s:14:"_image_benefit";s:25:"/uploads/insta-icon-1.png";}i:2;a:2:{s:5:"title";s:48:"Wide geography of search, more than 74 countries";s:14:"_image_benefit";s:25:"/uploads/money-icon-1.png";}}'
					],
					[
						'name' => 'testimonial_block_name',
						'value' => 'Linda Flores'
					],
					[
						'name' => 'testimonial_block_position',
						'value' => 'Businesswoman and Mother'
					],
					[
						'name' => 'testimonial_block_text',
						'value' => 'I travel a lot, but with this app I can keep myself in shape, Skillective is now my instructor 24/7. I highly recommend to people who value their time and health!'
					],
					[
						'name' => 'benefits_title',
						'value' => 'More benefits'
					],
					[
						'name' => '_image_form_block_bg',
						'value' => '/uploads/bg-user-register.jpg'
					],
					[
						'name' => '_image_testimonial_block_bg',
						'value' => '/uploads/single-testimonial-2.jpg'
					],
					[
						'name' => 'benefits',
						'value' => 'a:6:{i:0;a:2:{s:5:"title";s:13:"Benefit title";s:4:"text";s:180:"Book your favorite Social Media Influencers or Instructors near you through the Skillective Platform. See when their availble, where their available and lesson pricing in real time";}i:1;a:2:{s:5:"title";s:13:"Benefit title";s:4:"text";s:180:"Book your favorite Social Media Influencers or Instructors near you through the Skillective Platform. See when their availble, where their available and lesson pricing in real time";}i:2;a:2:{s:5:"title";s:13:"Benefit title";s:4:"text";s:180:"Book your favorite Social Media Influencers or Instructors near you through the Skillective Platform. See when their availble, where their available and lesson pricing in real time";}i:3;a:2:{s:5:"title";s:13:"Benefit title";s:4:"text";s:180:"Book your favorite Social Media Influencers or Instructors near you through the Skillective Platform. See when their availble, where their available and lesson pricing in real time";}i:4;a:2:{s:5:"title";s:13:"Benefit title";s:4:"text";s:180:"Book your favorite Social Media Influencers or Instructors near you through the Skillective Platform. See when their availble, where their available and lesson pricing in real time";}i:5;a:2:{s:5:"title";s:13:"Benefit title";s:4:"text";s:180:"Book your favorite Social Media Influencers or Instructors near you through the Skillective Platform. See when their availble, where their available and lesson pricing in real time";}}'
					],
				]
			],
			[
				'name'      => 'instructor/invite-instructor',
				'title'     => 'Invite instructors',
				'content'   => '<p>who want to share their work on Skillective</p>',
				'meta'      => [
					[
						'name' => 'how_this_works_title',
						'value' => 'How does it work?'
					],
					[
						'name' => 'how_this_works_blocks',
						'value' => ''
					],
				]
			],
			[
				'name'      => 'student/dashboard',
				'title'     => 'Dashboard',
				'content'   => '',
				'meta'      => [
					[
						'name' => 'invitation_form_title',
						'value' => 'Your favorite Instructor of Influencer is not on Skillective?'
					],
					[
						'name' => 'invitation_form_description',
						'value' => 'Submit their email or mobile phone number here and we will reach out to them to let them know lessons are waiting'
					]
				]
			],
			[
				'name'      => 'instructor/dashboard',
				'title'     => 'Instructor Dashboard',
				'content'   => '',
				'meta'      => [
					[
						'name' => 'invitation_form_title',
						'value' => 'Invite your favorite client on Skillective.'
					],
					[
						'name' => 'invitation_form_description',
						'value' => 'We will send him an email invitation and add to your clients list.'
					]
				]
			],
			[
				'name'      => 'instructor/gallery',
				'title'     => 'Gallery',
				'content'   => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. </p>',
			],
			[
				'name'      => 'student/gallery',
				'title'     => 'Gallery',
				'content'   => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. </p>',
			],
			[
				'name'      => 'profile/edit',
				'title'     => 'Profile Settings',
				'content'   => '',
				'meta'      => [
					[
						'name' => 'student_notifications_block_description',
						'value' => 'Choose your preferred method of being notified by Skillective! Don’t miss open time slots with your favorite instructor or influencer. This setting is how your instructor will contact you about your lesson.'
					],
					[
						'name' => 'instructor_notifications_block_description',
						'value' => 'Choose your preferred method of being notified by Skillective! This setting is how we notify you about clients booking requests.'
					]
				]
			],
        ];
*/

        $pages = [
            [
                'name'      => 'how-it-works',
                'title'     => 'How It Works',
                'content'   => '',
                'template'  => 'how-it-works',
                'meta'      => [
                ]
            ],
        ];

        foreach ($pages as $page) {
            $input = ['name' => $page['name'], 'title' => $page['title'],  'content' => $page['content']];
            if (isset($page['template']))
                $input['template'] = $page['template'];

            $pageModel = Page::create($input);

            if (isset($page['meta'])){
                foreach ($page['meta'] as $metaData) {
                    $metaModel = PageMeta::create(['page_id' => $pageModel->id, 'name'=>$metaData['name'], 'value'=>$metaData['value']]);
                }
            }
        }

        // copy images
        /*
        \File::copy(base_path('public/images/home-banner.jpg'),base_path('public/uploads/home-banner.jpg'));
        \File::copy(base_path('public/images/about-1.jpg'),base_path('public/uploads/about-1.jpg'));
        \File::copy(base_path('public/images/about-2.jpg'),base_path('public/uploads/about-2.jpg'));
        \File::copy(base_path('public/images/call-to-action.jpg'),base_path('public/uploads/call-to-action.jpg'));
		\File::copy(base_path('public/images/icon-qw.png'),base_path('public/uploads/icon-qw.png'));
		\File::copy(base_path('public/images/search-icon.svg'),base_path('public/uploads/search-icon.svg'));
		\File::copy(base_path('public/images/piplu-icon.svg'),base_path('public/uploads/piplu-icon.svg'));
		\File::copy(base_path('public/images/calendar-big-icon.svg'),base_path('public/uploads/calendar-big-icon.svg'));
		\File::copy(base_path('public/images/ok-icon.svg'),base_path('public/uploads/ok-icon.svg'));

		\File::copy(base_path('public/images/register-bg.jpg'),base_path('public/uploads/register-bg.jpg'));
		\File::copy(base_path('public/images/single-testimonial.jpg'),base_path('public/uploads/single-testimonial.jpg'));
		\File::copy(base_path('public/images/money-icon.png'),base_path('public/uploads/money-icon.png'));
		\File::copy(base_path('public/images/insta-icon.png'),base_path('public/uploads/insta-icon.png'));
		\File::copy(base_path('public/images/traner-img.png'),base_path('public/uploads/traner-img.png'));
		\File::copy(base_path('public/images/money-icon.png'),base_path('public/uploads/money-icon-1.png'));
		\File::copy(base_path('public/images/insta-icon.png'),base_path('public/uploads/insta-icon-1.png'));
		\File::copy(base_path('public/images/traner-img.png'),base_path('public/uploads/traner-img-1.png'));
		\File::copy(base_path('public/images/bg-user-register.jpg'),base_path('public/uploads/bg-user-register.jpg'));
		\File::copy(base_path('public/images/single-testimonial-2.jpg'),base_path('public/uploads/single-testimonial-2.jpg'));

		\File::copy(base_path('public/images/traner-img.png'),base_path('public/uploads/traner-img-.png'));
*/
    }
}
