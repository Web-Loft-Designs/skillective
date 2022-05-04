<?php

use Illuminate\Database\Seeder;
use Tylercd100\Placeholders\Facades\Placeholders;

class NotificationTableSeed extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 * @throws \Throwable
	 */
	public function run()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		DB::table('custom_notification_methods')->truncate();
		DB::table('custom_notifications')->truncate();
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');

		$defaultVars = [
			'app_name',
			'app_url',
			'current_year'
		];

		$lessonRequestNotificationVars = [
			'id',
			'student_name',
			'instructor_name',
			'lesson_start',
			'lesson_end',
			'lesson_datetime',
			'count_participants',
			'lesson_location',
			'lesson_genre',
			'lesson_price',
			'lesson_url',
			'student_note',
			'instructor_note',
			'hours_to_book',
			'hours_to_accept'
		];

		$bookingNotificationVars = [
			'id',
			'student_name',
			'instructor_name',
			'lesson_start',
			'lesson_end',
			'lesson_datetime',
			'lesson_location',
			'lesson_genre',
			'spot_price',
			'special_request',
			'time_to_approve_booking',
			'total_fee',
			'to_pay',
			'booking_url',
			'lesson_url'
		];

		$notifications = [
			[
				'tag'     => 'reset_password',
				'data'    => [
					'available_vars' => [
						'reset_url'
					],
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.password.apiResetLink")->render(),
						'data'    => [
							'subject' => 'Password Reset',
						],
					],
				],
			],
			[
				'tag'     => 'contact_us_form_request',
				'data'    => [
					'available_vars' => [
						'first_name',
						'last_name',
						'address',
						'mobile_phone',
						'email',
						'reason'
					],
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.contactUsFormRequest")->render(),
						'data'    => [
							'subject' => 'Contact Us form message',
						],
					],
				],
			],
			[
				'tag'     => 'student_registration_confirmation',
				'data'    => [
					'available_vars' => [],
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.student.registrationConfirmation")->render(),
						'data'    => [
							'subject' => 'Registration Confirmation',
						],
					],
				],
			],
			[
				'tag'     => 'user_account_suspended',
				'data'    => [
					'available_vars' => [
						'link',
					],
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.user.accountSuspended")->render(),
						'data'    => [
							'subject' => 'Account Suspended',
						],
					],
				],
			],
			[
				'tag'     => 'user_must_finish_registration',
				'data'    => [
					'available_vars' => [
						'link',
					],
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.student.studentMustFinishRegistration")->render(),
						'data'    => [
							'subject' => 'You have to finish Client registration',
						],
					],
				],
			],
			[
				'tag'     => 'student_registration_for_admins',
				'data'    => [
					'available_vars' => [
						'first_name',
						'last_name',
						'user_url',
					],
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.student.newRegistrationAdminNotification")->render(),
						'data'    => [
							'subject' => 'New Client Registered',
						],
					],
				],
			],
			[
				'tag'     => 'instructor_registration_confirmation',
				'data'    => [
					'available_vars' => [
						'instructor_name',
						'instructor_url',
					],
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.instructor.registrationRequestConfirmation")->render(),
						'data'    => [
							'subject' => 'Registration Confirmation',
						],
					],
				],
			],
			[
				'tag'     => 'instructor_registration_request_on_review',
				'data'    => [
					'available_vars' => [
						'first_name',
						'last_name'
					],
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.instructor.registrationRequestOnReview")->render(),
						'data'    => [
							'subject' => 'Instructor Registration Request',
						],
					],
				],
			],
			[
				'tag'     => 'instructor_registration_for_admins',
				'data'    => [
					'available_vars' => [
						'first_name',
						'last_name',
						'user_url',
					],
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.instructor.registrationRequestAdminNotification")->render(),
						'data'    => [
							'subject' => 'New Instructor Registration Request',
						],
					],
				],
			],
			[
				'tag'     => 'instructor_registration_request_denied',
				'data'    => [
					'available_vars' => [],
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.instructor.registrationRequestDenied")->render(),
						'data'    => [
							'subject' => 'Instructor Registration Request Denied',
						],
					],
				],
			],
			[
				'tag'     => 'instructor_registration_request_approved',
				'data'    => [
					'available_vars' => [
						'link'
					],
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.instructor.registrationRequestApproved")->render(),
						'data'    => [
							'subject' => 'Instructor Registration Request Approved',
						],
					],
				],
			],
			// bookings
			[
				'tag'     => 'booking_on_review_student_notification',
				'data'    => [
					'available_vars' => $bookingNotificationVars,
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.booking.bookingCreatedStudentConfirmation")->render(),
						'data'    => [
							'subject' => 'Your Booking Request has been sent for review',
						],
					],
				],
			],
			[
				'tag'     => 'booking_on_review_instructor_notification',
				'data'    => [
					'available_vars' => $bookingNotificationVars,
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.booking.bookingCreatedInstructorNotification")->render(),
						'data'    => [
							'subject' => 'New Booking Request has been sent for review',
						],
					],
					[
						'method'  => 'sms',
						"content" => "Confirm Skillective Booking Request from [[student_name]] for [[lesson_datetime]] at [[app_url]]/instructor/bookings?page=1&type=pending",
						'data'    => [
							'subject' => 'New Booking Request has been sent for review',
						],
					],
					[
						'method'  => 'whatsapp',
						"content" => "Your code is " . "Confirm Skillective Booking Request from [[student_name]] for [[lesson_datetime]] at [[app_url]]/instructor/bookings?page=1&type=pending",
						'data'    => [
							'subject' => 'New Booking Request has been sent for review',
						],
					],
				],
			],
			[
				'tag'     => 'booking_approved_student_notification',
				'data'    => [
					'available_vars' => $bookingNotificationVars,
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.booking.bookingApprovedStudentNotification")->render(),
						'data'    => [
							'subject' => 'Your Booking Request has been approved',
						],
					],
				],
			],
			/////////////
			[
				'tag'     => 'booking_payment_in_escrow_student_notification',
				'data'    => [
					'available_vars' => $bookingNotificationVars,
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.booking.bookingPaymentInEscrowStudentNotification")->render(),
						'data'    => [
							'subject' => 'Your Booking Payment hold in Escrow',
						],
					],
				],
			],
			[
				'tag'     => 'booking_payment_in_escrow_instructor_notification',
				'data'    => [
					'available_vars' => $bookingNotificationVars,
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.booking.bookingPaymentInEscrowInstructorNotification")->render(),
						'data'    => [
							'subject' => 'Your lesson booking payment is being held in escrow',
						],
					],
					[
						'method'  => 'sms',
						"content" => "Your lesson booking payment($[[to_pay]]) is being held in escrow (Booking ID: [[id]])",
						'data'    => [
							'subject' => 'Your lesson booking payment is being held in escrow',
						],
					],
				],
			],
			[
				'tag'     => 'booking_payment_in_escrow_admin_notification',
				'data'    => [
					'available_vars' => $bookingNotificationVars,
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.booking.bookingPaymentInEscrowAdminNotification")->render(),
						'data'    => [
							'subject' => 'Booking payment is being held in escrow',
						],
					],
				],
			],
			/////////////
			[
				'tag'     => 'booking_payment_disbursed_instructor_notification',
				'data'    => [
					'available_vars' => $bookingNotificationVars,
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.booking.bookingPaymentDisbursedInstructorNotification")->render(),
						'data'    => [
							'subject' => 'Your lesson payment has been disbursed from your account',
						],
					],
					[
						'method'  => 'sms',
						"content" => "Your lesson payment has been disbursed from your account",
						'data'    => [
							'subject' => 'Your lesson payment($[[to_pay]]) has been disbursed from your account (Booking ID: [[id]])',
						],
					],
				],
			],
			[
				'tag'     => 'booking_payment_disbursed_admin_notification',
				'data'    => [
					'available_vars' => $bookingNotificationVars,
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.booking.bookingPaymentDisbursedAdminNotification")->render(),
						'data'    => [
							'subject' => 'Booking payment has been disbursed',
						],
					],
				],
			],
			/////////////
			[
				'tag'     => 'booking_cant_release_transaction_admin_notification',
				'data'    => [
					'available_vars' => array_merge($bookingNotificationVars, ['reason']),
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.booking.bookingCantReleaseTransactionAdminNotification")->render(),
						'data'    => [
							'subject' => 'Booking payment can\'t be released from escrow',
						],
					],
				],
			],
			/////////////
			[
				'tag'     => 'booking_cancelled_student_notification',
				'data'    => [
					'available_vars' => $bookingNotificationVars,
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.booking.bookingCancelledStudentNotification")->render(),
						'data'    => [
							'subject' => 'Your Booking has been cancelled',
						],
					],
				],
			],
			[
				'tag'     => 'booking_cancelled_instructor_notification',
				'data'    => [
					'available_vars' => $bookingNotificationVars,
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.booking.bookingCancelledInstructorNotification")->render(),
						'data'    => [
							'subject' => 'Your Lesson Booking has been cancelled',
						],
					],
				],
			],
			[
				'tag'     => 'booking_cancelled_admin_notification',
				'data'    => [
					'available_vars' => $bookingNotificationVars,
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.booking.bookingCancelledAdminNotification")->render(),
						'data'    => [
							'subject' => 'Booking has been cancelled',
						],
					],
				],
			],
			[
				'tag'     => 'booking_automatically_cancelled_student_notification',
				'data'    => [
					'available_vars' => $bookingNotificationVars,
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.booking.bookingAutomaticallyCancelledStudentNotification")->render(),
						'data'    => [
							'subject' => 'Your Booking Request has been cancelled',
						],
					],
				],
			],
			[
				'tag'     => 'booking_automatically_cancelled_instructor_notification',
				'data'    => [
					'available_vars' => $bookingNotificationVars,
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.booking.bookingAutomaticallyCancelledInstructorNotification")->render(),
						'data'    => [
							'subject' => 'Your Lesson Booking Request has been cancelled',
						],
					],
				],
			],

			[
				'tag'     => 'lesson_request_created',
				'data'    => [
					'available_vars' => $lessonRequestNotificationVars,
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.lesson_request.lessonRequestCreatedNotification")->render(),
						'data'    => [
							'subject' => 'You have new Time Request',
						],
					],
				],
			],
			[
				'tag'     => 'lesson_request_approved',
				'data'    => [
					'available_vars' => $lessonRequestNotificationVars,
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.lesson_request.lessonRequestApprovedNotification")->render(),
						'data'    => [
							'subject' => 'Time Request has been approved',
						],
					],
				],
			],
			[
				'tag'     => 'lesson_request_cancelled',
				'data'    => [
					'available_vars' => $lessonRequestNotificationVars,
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.lesson_request.lessonRequestCancelledNotification")->render(),
						'data'    => [
							'subject' => 'Time Request has been cancelled',
						],
					],
				],
			],

			[
				'tag'     => 'custom_user_notification',
				'data'    => [
					'available_vars' => [
						'sender_first_name',
						'sender_last_name',
						'recepient_first_name',
						'recepient_last_name',
						'message'
					],
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.customUserNotification")->render(),
						'data'    => [
							'subject' => 'Notification From ' . config('app.name'),
						],
					],
					[
						'method'  => 'sms',
						"content" => "[[message]]",
						'data'    => [
							'subject' => 'Notification From ' . config('app.name'),
						],
					],
					[
						'method'  => 'whatsapp',
						"content" => "Your code is [[message]]",
						'data'    => [
							'subject' => 'Notification From ' . config('app.name'),
						],
					],
				],
			],
			[
				'tag'     => 'you_may_be_interested_in_lesson_notification',
				'data'    => [
					'available_vars' => [
						'id',
						'instructor_id',
						'instructor_name',
						'instructor_instagram',
						'lesson_date',
						'lesson_start_time',
						'lesson_end_time',
						'lesson_start',
						'lesson_end',
						'lesson_location',
						'lesson_genre',
						'spot_price',
						'lesson_url',
						'instructor_profile_url',
					],
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.lesson.youMayBeInterestedInLessonNotification")->render(),
						'data'    => [
							'subject' => 'New Lesson of your interest on ' . config('app.name'),
						],
					],
					[
						'method'  => 'sms',
						"content" => "The Skillective Instructor @[[instructor_instagram]] has opened lesson availability near you. Click [[app_url]]/profile/[[instructor_id]] to view their lesson availability.",
						'data'    => [
							'subject' => 'New Lesson of your interest on ' . config('app.name')
						],
					],
					[
						'method'  => 'whatsapp',
						"content" => "Your code is " . "The Skillective Instructor @[[instructor_instagram]] has opened lesson availability near you. Click [[app_url]]/profile/[[instructor_id]] to view their lesson availability.",
						'data'    => [
							'subject' => 'New Lesson of your interest on ' . config('app.name')
						],
					],
				],
			],
			[
				'tag'     => 'you_may_be_interested_in_virtual_lesson_notification',
				'data'    => [
					'available_vars' => [
						'id',
						'instructor_id',
						'instructor_name',
						'instructor_instagram',
						'lesson_date',
						'lesson_start_time',
						'lesson_end_time',
						'lesson_start',
						'lesson_end',
						'lesson_location',
						'lesson_timezone',
						'lesson_genre',
						'spot_price',
						'lesson_url',
						'instructor_profile_url',
					],
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.lesson.youMayBeInterestedInVirtualLessonNotification")->render(),
						'data'    => [
							'subject' => 'New Virtual Lesson of your interest on ' . config('app.name'),
						],
					],
					[
						'method'  => 'sms',
						"content" => "The Skillective Instructor @[[instructor_instagram]] has opened virtual lesson availability. Click [[app_url]]/profile/[[instructor_id]] to view their lesson availability.",
						'data'    => [
							'subject' => 'New Virtual Lesson of your interest on ' . config('app.name')
						],
					],
					[
						'method'  => 'whatsapp',
						"content" => "Your code is " . "The Skillective Instructor @[[instructor_instagram]] has opened virtual lesson availability. Click [[app_url]]/profile/[[instructor_id]] to view their lesson availability.",
						'data'    => [
							'subject' => 'New Virtual Lesson of your interest on ' . config('app.name')
						],
					],
				],
			],
			[
				'tag'     => 'instructor_invitation',
				'data'    => [
					'available_vars' => [
						'sender_name',
						'recepient_name',
						'registration_url'
					],
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.instructor.registrationInvitation")->render(),
						'data'    => [
							'subject' => 'Invitation to register at ' . config('app.name'),
						],
					],
					[
						'method'  => 'sms',
						"content" => 'Congratulations[[recepient_name]]! You have been invited by [[sender_name]] to create an instructor account on [[app_name]]. We are a platform where influencers and Instructors can be booked & paid by their clients for private lessons. Currently our platform is invitation only, which makes this recommendation not only really exciting but and honor!  Follow the link [[registration_url]] to submit your information!',
						'data'    => [
							'subject' => 'Invitation to register at ' . config('app.name'),
						],
					]
				],
			],
			[
				'tag'     => 'student_invitation',
				'data'    => [
					'available_vars' => [
						'sender_name',
						'sender_profile_url',
						'registration_url'
					],
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.student.registrationInvitation")->render(),
						'data'    => [
							'subject' => 'Invitation to register at ' . config('app.name'),
						],
					],
					[
						'method'  => 'sms',
						"content" => 'Greetings! Your Instructor [[sender_name]] has started posting their private lesson availability on Skillective. Follow the link [[sender_profile_url]] to view their availability!',
						'data'    => [
							'subject' => 'Invitation to register at ' . config('app.name'),
						],
					]
				],
			],
			[
				'tag'     => 'invite_new_instructor_request',
				'data'    => [
					'available_vars' => [
						'sender_name',
						'registration_url',
						'recepient_name',
						'invited_contact'
					],
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.admin.inviteNewInstructorRequest")->render(),
						'data'    => [
							'subject' => 'Request to invite new Instructor',
						],
					]
				],
			],

			[
				'tag'     => 'share_bookings_schedule',
				'data'    => [
					'available_vars' => [
						'user_name'
					],
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.share.bookings_schedule")->render(),
						'data'    => [
							'subject' => 'Booked lessons schedule (' . config('app.name') . ')',
						],
					]
				],
			],
			[
				'tag'     => 'booking_cancellation_request',
				'data'    => [
					'available_vars' => [
						'booking_id',
						'booking_url',
						'lesson_url',
						'lesson_location',
						'lesson_start',
						'lesson_genre',
						'student_name',
						'to_pay'
					],
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.booking.booking_cancellation_request")->render(),
						'data'    => [
							'subject' => 'Your Client has requested a Cancellation',
						],
					]
				],
			],

			[
				'tag'     => 'sub_merchant_account_approved',
				'data'    => [
					'available_vars' => [
						'login_url',
					],
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.merchant_account.sub_merchant_account_approved")->render(),
						'data'    => [
							'subject' => 'Your Merchant Account approved (' . config('app.name') . ')',
						],
					]
				],
			],
			[
				'tag'     => 'sub_merchant_account_declined',
				'data'    => [
					'available_vars' => [
						'merchant_data_edit_url',
						'reason',
					],
				],
				'methods' => [
					[
						'method'  => 'mail',
						"content" => view("emails.merchant_account.sub_merchant_account_declined")->render(),
						'data'    => [
							'subject' => 'Your Merchant Account declined (' . config('app.name') . ')',
						],
					]
				],
			],
			[
				'tag' => 'booking_in_24_hour',
				'data' => [
					'available_vars' => [
						'lesson_genre',
						'lesson_start'
					]
				],
				'methods' => [
					[
						'method' => 'mail',
						"content" => view("emails.booking.booking_in_24_hour")->render(),
						'data'    => [
							'subject' => 'Lesson on Skillective start in 24 hours',
						],
					]
				]
			],
			[
				'tag' => 'booking_in_1_hour',
				'data' => [
					'available_vars' => [
						'lesson_genre',
						'lesson_start'
					]
				],
				'methods' => [
					[
						'method' => 'mail',
						"content" => view("emails.booking.booking_in_1_hour")->render(),
						'data'    => [
							'subject' => 'Lesson on Skillective start in 1 hour',
						],
					]
				]
			],
			[
				'tag' => 'new_promo_code',
				'data' => [
					'available_vars' => [
						'instructor_name',
						'amount',
						'lesson_type',
						'finish',
						'promo_name'
					]
				],
				'methods' => [
					[
						'method' => 'mail',
						"content" => view("emails.student.promo_code")->render(),
						'data'    => [
							'subject' => 'You have a new promo code on Skillective.com!',
						],
					]
				]
			]
		];

		foreach ($notifications as $notification) {
			/** @var \App\Models\CustomNotification $model */
			$params = array_only($notification, [
				'tag',
				'data',
			]);
			$params['data'] = array_merge($defaultVars, $params['data']);
			$notificationModel = \App\Models\CustomNotification::updateOrCreate(['tag' => $notification['tag']], $params);
			foreach ($notification['methods'] as $method => $data) {
				$data = collect($data);
				$methodModel = \App\Models\CustomNotificationMethod::query()->updateOrCreate($data->only([
					'method',
				])->toArray() + ['custom_notification_id' => $notificationModel->getKey()], $data->toArray());
				$methodModel->notification()->associate($notificationModel);
				$methodModel->save();
			}
		}
	}
}
