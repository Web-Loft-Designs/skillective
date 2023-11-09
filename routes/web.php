<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


use App\Http\Controllers\Auth\InstructorRegisterController;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\GlobalShopController;
use App\Http\Controllers\InstructorGalleryController;
use Illuminate\Support\Facades\Route;

Route::get('/', 'HomeController@index')->name('home');
Route::get('/lessons', 'LessonsPageController@index')->name('lessons');
Route::get('/instructors', 'InstructorsPageController@index')->name('instructors');
Route::get('/lesson/{lesson}', 'LessonPageController@index')->name('lesson');


//Route::get('/braintree/webhook', 'BraintreeWebhookController@index'); // TODO: remove this route
Route::post('/braintree/webhook', 'BraintreeWebhookController@index')->middleware(['guest']);

//Auth::routes();
Route::group(['middleware'=>['guest', 'web']], function () {
// frontend users login
	Route::get('login', 'Auth\FrontendLoginController@showLoginForm')->name('frontend.login');
	Route::post('login', 'Auth\FrontendLoginController@login')->name('frontend.login');
// logging in for Admins
	Route::get('backend/login', 'Auth\BackendLoginController@showLoginForm')->name('login')->middleware(['rememberHttpReferer', 'web']);
	Route::post('backend/login', 'Auth\BackendLoginController@login')->name('login');
// Instructor registration
	Route::get('instructor/register', [InstructorRegisterController::class, 'showRegistrationForm'])->name('instructor.register');
	// registration via api
// Student registration
	Route::get('student/register', 'Auth\StudentRegisterController@showRegistrationForm')->name('student.register');
	// registration via api

// General Password Reset
	Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
//	Route::post('password/email', 'Auth\ForgotPasswordController@checkFinishRegistration')->name('password.email');
	Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
	Route::post('password/reset', 'Auth\ResetPasswordController@reset');
	Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
	Route::get('registration/finish', 'Auth\FinishRegistrationController@index')->name('registration.finish');
});

// Logout
Route::post('logout', 'Auth\FrontendLoginController@logout')->name('logout');

Route::get('/profile/edit/{user}', 'ProfileController@edit')->name('profile.edituser')->middleware(['role:Admin', 'web']); // edit user profile
Route::get('/profile/edit', 'ProfileController@edit')->name('profile.edit')->middleware(['role:Instructor|Student',  'web']); // edit own profile
Route::get('/profile/{user?}', 'ProfileController@show')->name('profile'); // user public profile

// Socialite Register Routes
Route::get('/social/redirect/{provider}', [SocialController::class, 'getSocialRedirect'])->name('social.redirect');
Route::get('/social/handle/{provider}', [SocialController::class, 'getSocialHandle'])->name('social.handle');
Route::get('/social/{provider}/instructor/registration', [SocialController::class, 'getSocialInstructorRegistration'])
    ->name('social.instructor.registration')
    ->middleware(['guest' ,  'web']);
Route::get('/social/ig/load-media', [SocialController::class, 'getSocialInstagramMedia'])
    ->name('social.instagram.media.update')
    ->middleware(['role:Instructor',  'web']);
Route::post('/social/detach/{provider}', [SocialController::class, 'detachSocial'])
    ->middleware(['auth',  'web'])
    ->name('social.detach');


Route::get('/globalshop', [GlobalShopController::class, 'index'])->name('globalshop');
Route::get('/checkout', 'CheckoutController@index')->name('checkout');
Route::get('/cart', 'CartController@index')->name('cart');

Route::group(['prefix' => 'instructor', 'middleware'=>['role:Instructor',  'web']], function () {
	Route::get('/dashboard', 'InstructorDashboardController@index')->name('instructor.dashboard');
	Route::get('/schedule', 'InstructorScheduleController@index')->name('instructor.schedule');
	Route::get('/bookings', 'InstructorBookingsController@index')->name('instructor.bookings');
	Route::get('/clients', 'InstructorClientsController@index')->name('instructor.clients');
	Route::get('/gallery', [InstructorGalleryController::class,'index'])->name('instructor.gallery');


	Route::get('/incomes', 'InstructorIncomesController@index')->name('instructor.incomes');
	Route::get('/payouts', 'InstructorPayoutsController@index')->name('instructor.payouts');
	Route::get('/invite-instructor', 'InstructorInvitesController@index')->name('instructor.invites');

	Route::get('/my-shop', 'InstructorMyShopController@index')->name('instructor.my-shop');
	Route::get('/my-shop/video/{video}', 'InstructorMyShopVideoController@index')->name('instructor.my-shop.video');

	Route::get('/discount-management', 'InstructorDiscountManagementController@index')->name('instructor.discount-management');
});

Route::group(['prefix' => 'student', 'middleware'=>['role:Student',  'web']], function () {
	Route::get('/dashboard', 'StudentDashboardController@index')->name('student.dashboard');
	Route::get('/schedule', 'StudentScheduleController@index')->name('student.schedule');
	Route::get('/bookings', 'StudentBookingsController@index')->name('student.bookings');
	Route::get('/instructors', 'StudentInstructorsController@index')->name('student.instructors');
	Route::get('/gallery', 'StudentGalleryController@index')->name('student.gallery');


	Route::get('/library', 'StudentLibraryController@index')->name('student.library');
	Route::get('/library/video/{video}', 'StudentLibraryVideoController@index')->name('student.library.video');
});


// Virtual lessons
Route::post('lesson/room-events', 'VirtualLessonRoomEventsController@handle')->name('twiliocallbackhandler')->middleware(['guest',  'web']);
// open videochat window
Route::group(['middleware'=>['role:Instructor|Student',  'web']], function () {
    Route::get('/virtual-lesson/{lesson}', 'VirtualLessonPageController@index')->name('virtual-lesson');
});

// BACKEND ROUTES
Route::group(['prefix' => 'backend', 'middleware'=>['rememberHttpReferer', 'role:Admin',  'web']], function () {

	Route::post('/login-as', 'Backend\LoginAsController@loginAs');

	Route::get('/', 'Backend\DashboardController@index')->name('backend.dashboard');

	Route::resource('/genres', 'Backend\GenreController', ['as' => 'backend']);
	Route::resource('/genre-categories', 'Backend\GenreCategoryController', ['as' => 'backend']);

    Route::resource('/faqs', 'Backend\FaqController', ['as' => 'backend']);
    Route::resource('/faq-categories', 'Backend\FaqCategoryController', ['as' => 'backend']);

	Route::get('lessons', 'Backend\LessonController@index')->name('backend.lessons.index'); //	Route::resource('lessons', 'Backend\LessonController', ['as' => 'backend']);

	Route::resource('/testimonials', 'Backend\TestimonialController', ['as' => 'backend']);

	Route::get('/instructors', 'Backend\InstructorController@index')->name('backend.instructors.index');
	Route::get('/students', 'Backend\StudentController@index')->name('backend.students.index');

	Route::get('/pages', 'Backend\PageController@index')->name('backend.pages');
	Route::get('/page/add', 'Backend\PageController@add')->name('backend.page.create');
	Route::post('/page/add', 'Backend\PageController@store')->name('backend.page.create');
	Route::get('/page/{page}', 'Backend\PageController@edit')->name('backend.page.edit');
	Route::post('/page/{page}', 'Backend\PageController@update')->name('backend.page.update');
	Route::delete('/page/{page}', 'Backend\PageController@destroy')->name('backend.page.delete');

	Route::get('/settings', 'Backend\SettingsController@index')->name('backend.settings');
	Route::post('/settings', 'Backend\SettingsController@update')->name('backend.settings.update');

	Route::resource('notifications', 'Backend\NotificationController', [
		'as'    =>  'backend'
	]);

	Route::get('/payments', 'Backend\PaymentsController@index')->name('backend.payments.index');

	// Reports
	Route::get('/reports/demographic', 'Backend\ReportsController@demographic')->name('backend.reports.demographic');
	Route::get('/reports/demographic/export', 'Backend\ReportsController@demographicExport')->name('backend.reports.demographic.export');
	Route::get('/reports/geographics', 'Backend\ReportsController@geographics')->name('backend.reports.geographics');
	Route::get('/reports/geographics/export', 'Backend\ReportsController@geographicsExport')->name('backend.reports.geographics.export');
	Route::get('/reports/other', 'Backend\ReportsController@other')->name('backend.reports.other');
	Route::get('/reports/other/export', 'Backend\ReportsController@otherExport')->name('backend.reports.other.export');

	Route::get('/profile/edit', 'Backend\ProfileController@edit')->name('admin.profile.edit');
});

Route::group(['middleware' => ['role:Admin']], function () {
	Route::get('/laravel-filemanager', '\UniSharp\LaravelFilemanager\Controllers\LfmController@show');
	Route::post('/laravel-filemanager/upload', '\UniSharp\LaravelFilemanager\Controllers\UploadController@upload');
	// list all lfm routes here...
});

Route::any('{all}', 'PageController@index')->where('all', '.*');
