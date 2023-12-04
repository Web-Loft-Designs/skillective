<?php

use App\Http\Controllers\API\CartAPIController;
use App\Http\Controllers\API\InstructorBookingsAPIController;
use App\Http\Controllers\API\PreRLessonsAPIController;
use App\Http\Controllers\API\StudentBookingsAPIController;
use App\Http\Controllers\API\StudentPaymentMethodsAPIController;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/create-to-client-list', 'StudentAddClientListAPIController@createToClientList');
Route::post('/add-to-client-list', 'StudentAddClientListAPIController@addToClientList');

Route::post('/contact-us', 'ContactUsAPIController@send');
Route::post('/become-instructor', 'ContactUsAPIController@becomeInstructor');

Route::group(['middleware' => ['guest']], function () {

	Route::post('login', '\App\Http\Controllers\Auth\FrontendLoginController@login')->name('frontend.login');
	Route::post('instructor/remember', 'Auth\InstructorRegisterController@remember'); // registration in social controller after redirect
	Route::post('student/register', 'Auth\StudentRegisterController@register'); // Flow for form with input data
	Route::get('/user/exists', 'Auth\UserFinishRegistrationController@exists'); // accepts email/token get params(required)
	Route::post('/user/finish-registration', 'Auth\UserFinishRegistrationController@finishRegistration');
});

Route::get('/us-states', 'USStatesAPIController@index');
Route::get('/cetegorized-genres', 'GenreAPIController@categorizedGenres');
Route::get('/genres', 'GenreAPIController@index');
Route::get('/genres/featured', 'GenreAPIController@featured');
Route::get('/search/instructors', 'SearchAPIController@autocompleteInstructor');
Route::get('/search/genre', 'SearchAPIController@autocompleteGenres');
Route::get('/search/location', 'SearchAPIController@autocompleteLocations');
Route::get('user/{user}/media', 'MediaAPIController@index');

Route::group(['middleware' => ['role:Instructor|Student']], function () {
	Route::get('lesson-requests', 'LessonRequestAPIController@index');
	Route::post('lesson-request/{lessonRequest}/cancel', 'LessonRequestAPIController@cancel');
	Route::post('/invite-instructor', 'InvitationAPIController@inviteInstructor');
	Route::post('/invite-student', 'InvitationAPIController@inviteStudent');
	Route::get('virtual-lessons/{lesson}/room', 'VirtualLessonRoomsController@getRoomConnectSettings');
	Route::post('virtual-lessons/{lesson}/room/join', 'VirtualLessonRoomsController@getLessonAccessToken'); // to prevent open the virtual lesson url manually
	Route::get('virtual-lessons/rooms', 'VirtualLessonRoomsController@getList');
});

Route::group(['middleware' => ['role:Instructor|Student|Admin']], function () {
	Route::post('/send-notifications', 'NotificationsAPIController@notify');
});

Route::group(['prefix' => 'user', 'middleware' => ['role:Instructor|Student|Admin']], function () {

	Route::get('', 'UserAPIController@getUserData')->middleware(['role:Instructor|Student']);

	Route::post('/profile-image', 'ProfileImageAPIController@update');
	Route::delete('/profile-image', 'ProfileImageAPIController@delete');
	Route::post('/profile-image/{user}', 'ProfileImageAPIController@update')->middleware(['role:Admin']);
	Route::delete('/profile-image/{user}', 'ProfileImageAPIController@delete')->middleware(['role:Admin']);

	Route::post('/media', 'MediaAPIController@store');
	Route::delete('/media/{media}', 'MediaAPIController@destroy');

	Route::put('/password', 'UserAPIController@updatePassword');
	Route::put('/password/{user}', 'UserAPIController@updatePassword')->middleware(['role:Admin']);

	Route::put('/profile', 'UserAPIController@updateProfile');
	Route::put('/profile/{user}', 'UserAPIController@updateProfile')->middleware(['role:Admin']); // update foreign profiles

	Route::put('/notification_methods', 'UserAPIController@updateNotificationMethods');
	Route::put('/notification_methods/{user}', 'UserAPIController@updateNotificationMethods')->middleware(['role:Admin']);

	Route::get('/geo-locations/{user}', 'UserGeoLocationAPIController@index')->middleware(['role:Admin']);
	Route::put('/geo-locations/{user}', 'UserGeoLocationAPIController@update')->middleware(['role:Admin']);
	Route::get('/geo-locations', 'UserGeoLocationAPIController@index');
	Route::put('/geo-locations', 'UserGeoLocationAPIController@update');
	Route::delete('/geo-locations/{geolocation}', 'UserGeoLocationAPIController@destroy');
});

Route::get('lessons/search', 'SearchLessonsAPIController@index'); // search lessons
Route::get('lessons/upcoming-nearby/{lesson}', 'SearchLessonsAPIController@getSameDayUpcomingNearbyLessonLocationLessons');

Route::get('pre-r-lesson', 'PreRLessonsAPIController@index');
Route::get('/pre-r-lesson/instructor/{instructor}', [PreRLessonsAPIController::class, 'getPreRecordedLessonsByInstructorId']);
Route::get('instructors/search', 'SearchInstructorsAPIController@index'); // search instructors

Route::get('lesson/{lesson}', 'LessonsAPIController@details');
Route::get('upcoming-lessons', 'LessonsAPIController@upcomingLessons');


Route::get('instructor/{instructor}/lessons', 'InstructorLessonsAPIController@index'); // current instructor lessons


Route::group(['middleware' => ['role:Instructor']], function () {

	Route::post('lesson-request/{lessonRequest}/accept', 'LessonRequestAPIController@accept');
	Route::post('instructor/uploud-video', 'UploadVideoAPIController@upload');


	Route::group(['prefix' => 'instructor/virtual-lessons'], function () {
		Route::get('/', 'InstructorVirtualLessonRoomsController@index'); // current instructor lessons
		Route::delete('{lesson}/room/complete', 'InstructorVirtualLessonRoomsController@completeRoom');
		Route::delete('{lesson}/room/participant/{participantId}', 'InstructorVirtualLessonRoomsController@disconnectParticipantFromRoom');
	});

	Route::get('instructor/goal', 'InstructorGoalAPIController@get');
	Route::put('instructor/goal', 'InstructorGoalAPIController@update');
	Route::delete('instructor/goal', 'InstructorGoalAPIController@delete');

	Route::get('instructor/lessons', 'InstructorLessonsAPIController@index'); // current instructor lessons
	Route::get('instructor/lessons/meta', 'InstructorLessonsAPIController@pendingLessonsCount');
	Route::get('instructor/lessons/dashboard', 'InstructorLessonsAPIController@dashboardLessons');
	Route::get('instructor/lessons/export', 'InstructorLessonsAPIController@export'); // current instructor lessons EXPORT

	Route::post('instructor/lesson', 'InstructorLessonsAPIController@store');
	Route::put('instructor/lesson/{lesson}', 'InstructorLessonsAPIController@update');
    Route::post('instructor/lesson/upload-preview', 'UploadPreviewLessonAPIController@uploadPreview');


	Route::get('instructor/discount/{id}', 'InstructorDiscountsAPIController@indexDiscounts');
	Route::get('instructor/promo/{id}', 'InstructorDiscountsAPIController@indexPromoCodes');
	Route::post('instructor/discount', 'InstructorDiscountsAPIController@storeDiscount');
	Route::post('instructor/promo', 'InstructorDiscountsAPIController@storePromoCode');

	Route::put('instructor/discount/{discount}', 'InstructorDiscountsAPIController@updateDiscount');
	Route::put('instructor/promo/{promo}', 'InstructorDiscountsAPIController@updatePromo');

	Route::delete('instructor/discount/{discount}', 'InstructorDiscountsAPIController@deleteDiscount');
	Route::delete('instructor/promo/{promo}', 'InstructorDiscountsAPIController@deletePromo');

	Route::get('instructor/pre-r-lesson', 'PreRLessonsInstructorAPIController@index');
	Route::get('instructor/pre-r-lesson/genres', 'PreRLessonsInstructorAPIController@getAvailableGenres');
	Route::get('instructor/pre-r-lesson/{lesson}', 'PreRLessonsInstructorAPIController@getInstructorLessonById');
	Route::post('instructor/pre-r-lesson', 'PreRLessonsInstructorAPIController@store');
	Route::put('instructor/pre-r-lesson/{lesson}', 'PreRLessonsInstructorAPIController@update');
	Route::delete('instructor/pre-r-lesson/{lesson}', 'PreRLessonsInstructorAPIController@remove');

	Route::get('instructor/bookings', 'InstructorBookingsAPIController@index'); // current instructor bookings
//	Route::get('instructor/bookings/past', 'InstructorBookingsAPIController@index'); // past instructor bookings
	Route::post('instructor/booking/{booking}/approve', 'InstructorBookingsAPIController@approve'); // + payment process
	Route::post('instructor/booking/{booking}/reject', 'InstructorBookingsAPIController@reject');
	Route::post('instructor/bookings/cancel', 'InstructorBookingsAPIController@cancelMany'); // + money return
	Route::delete('instructor/booking/{booking}', [InstructorBookingsAPIController::class, 'cancel']); // cancel approved booking
	Route::put('instructor/booking/approve/{booking}', 'InstructorBookingsAPIController@approve'); // confirm pending booking

	Route::get('instructor/clients', 'InstructorClientsAPIController@index'); // current instructor clients
	Route::post('instructor/clients', 'InstructorClientsAPIController@add'); // add many
	Route::post('instructor/clients/remove', 'InstructorClientsAPIController@removeMany');
	Route::delete('instructor/client/{client}', 'InstructorClientsAPIController@remove'); // remove client

	Route::get('instructor/merchant', 'InstructorMerchantAPIController@get'); // current instructor clients
	Route::post('instructor/merchant', 'InstructorMerchantAPIController@create');
	Route::put('instructor/merchant', 'InstructorMerchantAPIController@update');

	Route::get('students', 'StudentsAPIController@index'); // get students list to add as instructor clients

	Route::get('instructor/incomes/{year?}', 'InstructorIncomesAPIController@index');

	Route::get('instructor/payouts', 'InstructorPayoutsAPIController@index');
});

// Крзина та оплата
Route::prefix('/cart')->group(function () {
    Route::post('/vault-setup-token', [StudentPaymentMethodsAPIController::class, 'getPpVaultSetupToken']);
    Route::get('/', [CartAPIController::class, 'index']);
    Route::get('/has-items', [CartAPIController::class, 'isCartHasItems']);
    Route::get('/total', [CartAPIController::class, 'getCartSummary']);
    Route::post('/checkout', [CartAPIController::class, 'checkout']);
    Route::get('/promo/{promo}', [CartAPIController::class, 'checkIsPromoIsValid']);
    Route::post('/validate-user-info', [CartAPIController::class, 'validateUserData']);
});

Route::post('student/instructors', 'StudentInstructorsAPIController@add'); // add many

Route::group(['middleware' => ['role:Student']], function () {
	Route::post('cart', 'CartAPIController@store');
	Route::delete('cart/{cart}', 'CartAPIController@delete');

	Route::post('lesson-request', 'LessonRequestAPIController@store');
	Route::get('student/bookings', 'StudentBookingsAPIController@index'); // current student bookings
	Route::get('student/bookings/export', 'StudentBookingsAPIController@export'); // current instructor lessons EXPORT
	Route::delete('student/booking/{booking}', [StudentBookingsAPIController::class, 'cancel']); // request cancellation booking
	Route::post('student/bookings/cancel', 'StudentBookingsAPIController@cancelMany'); // request cancellation many
	Route::get('student/bookings/past', 'StudentBookingsAPIController@index'); // past student bookings
	Route::post('student/bookings/share', 'StudentBookingsAPIController@share'); // share student bookings calendar

	Route::get('student/pre-r-lessons', 'StudentLibraryAPIController@index');
	Route::get('student/pre-r-lessons/{lesson}', 'StudentLibraryAPIController@getStudentLessonById');

	Route::get('student/instructors', 'StudentInstructorsAPIController@index'); // current student instructors

	Route::post('student/instructors/remove', 'StudentInstructorsAPIController@removeMany');
	Route::delete('student/instructor/{instructor}', 'StudentInstructorsAPIController@remove'); // remove instructor

	Route::post('student/instructor/favorite/{instructor}', 'StudentInstructorsAPIController@addAndMarkAsFavorite');
	Route::delete('student/instructor/favorite/{instructor}', 'StudentInstructorsAPIController@removeFromFavorites');

	Route::post('student/instructor/geo-notifications/{instructor}', 'StudentInstructorsAPIController@enableGeoNotifications');
	Route::delete('student/instructor/geo-notifications/{instructor}', 'StudentInstructorsAPIController@disableGeoNotifications');

	Route::post('student/instructor/virtual-lesson-notifications/{instructor}', 'StudentInstructorsAPIController@enableVirtualLessonNotifications');
	Route::delete('student/instructor/virtual-lesson-notifications/{instructor}', 'StudentInstructorsAPIController@disableVirtualLessonNotifications');

	Route::get('instructors', 'InstructorsAPIController@index'); // get instructors list to add as student instructors

	Route::get('student/payment-methods', [StudentPaymentMethodsAPIController::class, 'index']); // get student payment methods
    Route::post('student/vault-setup-token', [StudentPaymentMethodsAPIController::class, 'getPpVaultSetupToken']);
	Route::post('student/payment-method', [StudentPaymentMethodsAPIController::class, 'store']); // add student payment method
	Route::delete('student/payment-method/{paymentMethodToken}', [StudentPaymentMethodsAPIController::class, 'delete']); // delete student payment method data

    Route::get('student/genres', 'StudentLibraryAPIController@getStudentGenres');

});


Route::get('featured-instructors', 'InstructorsAPIController@getFeaturedInstructors');
Route::get('relation-instructors/{instructor}', 'InstructorsAPIController@getRelationInstructors'); // get relation instructors


Route::group(['middleware' => ['role:Admin|Instructor']], function () {
	Route::delete('lesson/{lesson}', 'LessonsAPIController@cancel');
	Route::post('lessons/cancel', 'LessonsAPIController@cancelMany');
});

Route::group(['prefix' => 'admin', 'middleware' => ['role:Admin']], function () {

	Route::put('/profile', 'Backend\AdminProfileAPIController@updateProfile');

	Route::get('lessons', 'Backend\LessonsAPIController@index');

	Route::post('/user/{user}/media', 'Backend\MediaAPIController@store');
	Route::delete('/user/media/{media}', 'Backend\MediaAPIController@destroy');

	Route::get('instructors', 'Backend\InstructorsAPIController@index');
	Route::put('instructors/featured/{user}', 'Backend\InstructorsAPIController@toggleFeatured');
	Route::put('instructors/priority/{user}', 'Backend\InstructorsAPIController@setPriority');
	Route::put('instructor/approve/{user}', 'Backend\InstructorsAPIController@approve');
	Route::put('instructor/deny/{user}', 'Backend\InstructorsAPIController@deny');
	Route::post('instructor/resend-registration-reminder/{user}', 'Backend\InstructorsAPIController@resendFinishRegistrationReminder');

	Route::get('bookings', 'Backend\PaymentsAPIController@index');
	Route::get('bookings/payments/export', 'Backend\PaymentsAPIController@export');

	Route::get('students', 'Backend\StudentsAPIController@index');

	Route::get('genres', 'Backend\GenresAPIController@index');
	Route::delete('genre/{genre}', 'Backend\GenresAPIController@disable');
	Route::post('genre/{genre}', 'Backend\GenresAPIController@enable');

	Route::get('genre-categories', 'Backend\GenreCategoriesAPIController@index');
	Route::delete('genre-categories/{category}', 'Backend\GenreCategoriesAPIController@delete');

	Route::get('faqs', 'Backend\FaqAPIController@index');
	Route::delete('faq/{genre}', 'Backend\FaqAPIController@delete');

	Route::get('faq-categories', 'Backend\FaqCategoryAPIController@index');
	Route::delete('faq-categories/{category}', 'Backend\FaqCategoryAPIController@delete');

	Route::put('users/count-invites-allowed/{user}', 'Backend\UsersAPIController@updateCountInvitesAllowed');
	Route::post('users/suspend', 'Backend\UsersAPIController@suspendMany');
	Route::delete('users/{user}', 'Backend\UsersAPIController@suspend');
	Route::delete('users/delete/{user}', 'Backend\UsersAPIController@deleteUser');

	Route::get('/invited', 'Backend\InvitationAPIController@getInvitations');
	Route::post('/invite-instructors', 'Backend\InvitationAPIController@inviteInstructors');
	Route::post('/invite-resend-instructors', 'Backend\InvitationAPIController@inviteResendInstructors');
	Route::post('/invite-students', 'Backend\InvitationAPIController@inviteStudents');

	Route::get('/reports/demographic', 'Backend\ReportsAPIController@demographic');
	Route::get('/reports/geographic', 'Backend\ReportsAPIController@geographics');
	Route::get('/reports/other', 'Backend\ReportsAPIController@other');
	Route::get('/reports/overview', 'Backend\ReportsAPIController@overview');
});
