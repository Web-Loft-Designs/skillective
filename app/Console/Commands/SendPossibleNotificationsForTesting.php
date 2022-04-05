<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\User;
use App\Models\Profile;
use App\Models\Lesson;
use App\Models\Invitation;
use \App\Repositories\BookingRepository;
use Illuminate\Console\Command;
use Faker\Generator as Faker;
use Notification;

use App\Notifications\ContactUsNotification;
use App\Notifications\StudentRegistrationConfirmation;
use App\Notifications\StudentMustFinishRegistration;
use App\Notifications\StudentRegistrationAdminNotification;
use App\Notifications\InstructorRegistrationRequestSentToReview;
use App\Notifications\InstructorRegistrationConfirmation;
use App\Notifications\InstructorRegistrationAdminNotification;
use App\Notifications\InstructorRegistrationRequestDenied;
use App\Notifications\InstructorRegistrationRequestApproved;
use App\Notifications\Bookings\BookingCreatedStudentConfirmation;
use App\Notifications\Bookings\BookingCreatedInstructorNotification;
use App\Notifications\Bookings\BookingApprovedStudentNotification;
use App\Notifications\Bookings\BookingCancelledStudentNotification;
use App\Notifications\Bookings\BookingCancelledInstructorNotification;
use App\Notifications\Bookings\BookingCancelledAdminNotification;
use App\Notifications\Bookings\BookingAutomaticallyCancelledStudentNotification;
use App\Notifications\Bookings\BookingAutomaticallyCancelledInstructorNotification;
use App\Notifications\InstructorRegistrationInvitation;
use App\Notifications\StudentRegistrationInvitation;
use App\Notifications\Admin\InviteNewInstructorRequest;
use App\Notifications\UserAccountSuspended;
use App\Notifications\Share\ShareBookingsSchedule;
use App\Notifications\Bookings\BookingCancellationRequestInstructorNotification;
use App\Notifications\MerchantAccount\SubMerchantAccountApproved;
use App\Notifications\MerchantAccount\SubMerchantAccountDeclined;
use App\Notifications\CustomUserNotification;
use App\Notifications\YouMayBeInterestedInLessonNotification;

class SendPossibleNotificationsForTesting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send_possible_notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "this is for testing notifications content";

    /**
     * Create a new command instance.
     *
     * @return void
     */

    private $bookingRepository = null;
    private $faker = null;

    public function __construct(BookingRepository $bookingRepository, Faker $faker)
    {
		$this->bookingRepository = $bookingRepository;
		$this->faker = $faker;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$fakeUser = new User();
//		sergio.lopez@webloftdesigns.com
		// skillective.test+allnotifications@gmail.com
		$fakeUser->email = 'skillective.test+allnotifications@gmail.com';
		$fakeUser->first_name = 'Hello';
		$fakeUser->last_name = 'Me';
		$fakeUser->bt_submerchant_status_reason = 'SOME REASON HERE';

		$fakeUserProfile = new Profile();
		$fakeUserProfile->mobile_phone = "+18178461102";
		$fakeUserProfile->notification_methods = array_keys(Profile::getAvailableNotificationMethods());
		$fakeUser->profile = $fakeUserProfile;

		$contactFormData = [
			'first_name' => $this->faker->firstName(),
			'last_name' => $this->faker->lastName(),
			'address' => $this->faker->address,
			'mobile_phone' => $this->faker->phoneNumber,
			'email' => $this->faker->email,
			'reason' => $this->faker->text(100),
		];
		$student = User::find(101);
		$instructor = User::find(40);
		$booking = Booking::find(3);
		$lesson = Lesson::find(147);
		$invitationInput = [
			'invited_email' => $fakeUser->getEmail(),
			'invited_mobile_phone' => $fakeUser->profile->mobile_phone,
		];
		$invitation = new Invitation($invitationInput);

		$fakeUser->notify(new BookingCreatedStudentConfirmation($booking));

		$fakeUser->notify(new BookingCreatedInstructorNotification($booking));
		
		return;

		Notification::route('mail', $fakeUser->getEmail())->notify(new ContactUsNotification($contactFormData));

		$fakeUser->notify(new StudentRegistrationConfirmation($student));

		$fakeUser->notify(new StudentMustFinishRegistration($student));

		$fakeUser->notify(new StudentRegistrationAdminNotification($student));

		$fakeUser->notify(new InstructorRegistrationRequestSentToReview($instructor));

		$fakeUser->notify(new InstructorRegistrationConfirmation($instructor));

		$fakeUser->notify(new UserAccountSuspended($instructor));

		$fakeUser->notify(new InstructorRegistrationAdminNotification($instructor));

		$fakeUser->notify(new InstructorRegistrationRequestDenied($instructor));

		$fakeUser->notify(new InstructorRegistrationRequestApproved($instructor));

		$fakeUser->notify(new BookingCreatedStudentConfirmation($booking));

		$fakeUser->notify(new BookingCreatedInstructorNotification($booking));

		$fakeUser->notify(new BookingApprovedStudentNotification($booking));

//		!!!!!!!!!!!! booking_payed_student_notification
//		!!!!!!!!!!!! booking_payed_instructor_notification
//		!!!!!!!!!!!! booking_payed_admin_notification

		$fakeUser->notify(new BookingCancelledStudentNotification($booking));

		$fakeUser->notify(new BookingCancelledInstructorNotification($booking));

		$fakeUser->notify(new BookingCancelledAdminNotification($booking));

		$fakeUser->notify(new BookingAutomaticallyCancelledStudentNotification($booking));

		$fakeUser->notify(new BookingAutomaticallyCancelledInstructorNotification($booking));

		Notification::send($invitation, new InstructorRegistrationInvitation($invitation, $fakeUserProfile->notification_methods));

		Notification::send($invitation, new StudentRegistrationInvitation($invitation, $fakeUserProfile->notification_methods));

		$fakeUser->notify(new InviteNewInstructorRequest($invitation));

		Notification::route('mail', $fakeUser->getEmail())->notify(new ShareBookingsSchedule($student, $this->bookingRepository));

		$fakeUser->notify(new BookingCancellationRequestInstructorNotification($booking));

		$fakeUser->notify(new SubMerchantAccountApproved($fakeUser));

		$fakeUser->notify(new SubMerchantAccountDeclined($fakeUser));

		$use_methods = array_keys(Profile::getAvailableNotificationMethods());
		Notification::send(collect($fakeUser), new CustomUserNotification('TEST MESSAGE', $student, $use_methods));

		$student->notify(new YouMayBeInterestedInLessonNotification($lesson));
    }
}