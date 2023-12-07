<?php

use App\Http\Controllers\Auth\BackendLoginController;
use App\Http\Controllers\Auth\FinishRegistrationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\FrontendLoginController;
use App\Http\Controllers\Auth\InstructorRegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\Auth\StudentRegisterController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\FaqCategoryController;
use App\Http\Controllers\Backend\FaqController;
use App\Http\Controllers\Backend\GenreCategoryController;
use App\Http\Controllers\Backend\GenreController;
use App\Http\Controllers\Backend\InstructorController;
use App\Http\Controllers\Backend\LessonController;
use App\Http\Controllers\Backend\LoginAsController;
use App\Http\Controllers\Backend\StudentController;
use App\Http\Controllers\Backend\TestimonialController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\GlobalShopController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstructorBookingsController;
use App\Http\Controllers\InstructorClientsController;
use App\Http\Controllers\InstructorDashboardController;
use App\Http\Controllers\InstructorDiscountManagementController;
use App\Http\Controllers\InstructorGalleryController;
use App\Http\Controllers\InstructorIncomesController;
use App\Http\Controllers\InstructorInvitesController;
use App\Http\Controllers\InstructorMyShopController;
use App\Http\Controllers\InstructorMyShopVideoController;
use App\Http\Controllers\InstructorPayoutsController;
use App\Http\Controllers\InstructorScheduleController;
use App\Http\Controllers\InstructorsPageController;
use App\Http\Controllers\LessonPageController;
use App\Http\Controllers\LessonsPageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentBookingsController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\StudentGalleryController;
use App\Http\Controllers\StudentInstructorsController;
use App\Http\Controllers\StudentLibraryController;
use App\Http\Controllers\StudentLibraryVideoController;
use App\Http\Controllers\StudentScheduleController;
use App\Http\Controllers\VirtualLessonPageController;
use App\Http\Controllers\VirtualLessonRoomEventsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class,'index'])->name('home');
Route::get('/lessons', [LessonsPageController::class,'index'])->name('lessons');
Route::get('/instructors', [InstructorsPageController::class,'index'])->name('instructors');
Route::get('/lesson/{lesson}', [LessonPageController::class,'index'])->name('lesson');

//Route::post('/braintree/webhook', 'BraintreeWebhookController@index')->middleware(['guest']);

//Auth::routes();
Route::group(['middleware'=>['guest']], function () {
// frontend users login
	Route::get('login', [FrontendLoginController::class, 'showLoginForm'])->name('frontend.login');
	Route::post('login', [FrontendLoginController::class,'login'])->name('frontend.login');
// logging in for Admins
	Route::get('backend/login', [BackendLoginController::class, 'showLoginForm'])->name('login')->middleware(['rememberHttpReferer']);
	Route::post('backend/login', [BackendLoginController::class,'login'])->name('login');
// Instructor registration
	Route::get('instructor/register', [InstructorRegisterController::class, 'showRegistrationForm'])->name('instructor.register');
	// registration via api
// Student registration
	Route::get('student/register',  [StudentRegisterController::class,'showRegistrationForm'])->name('student.register');
	// registration via api

// General Password Reset
	Route::post('password/email', [ForgotPasswordController::class,'sendResetLinkEmail'])->name('password.email');
	Route::get('password/reset', [ForgotPasswordController::class,'showLinkRequestForm'])->name('password.request');
	Route::post('password/reset', [ResetPasswordController::class,'reset']);
	Route::get('password/reset/{token}', [ResetPasswordController::class,'showResetForm'])->name('password.reset');
	Route::get('registration/finish', [FinishRegistrationController::class,'index'])->name('registration.finish');
});

// Logout
Route::post('logout', [FrontendLoginController::class,'logout'])->name('logout');

Route::get('/profile/edit/{user}', [ProfileController::class, 'edit'])->name('profile.edituser')->middleware(['role:Admin']); // edit user profile
Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit')->middleware(['role:Instructor|Student']); // edit own profile
Route::get('/profile/{user?}', [ProfileController::class, 'show'])->name('profile'); // user public profile

// Socialite Register Routes
Route::get('/social/redirect/{provider}', [SocialController::class, 'getSocialRedirect'])->name('social.redirect');
Route::get('/social/handle/{provider}', [SocialController::class, 'getSocialHandle'])->name('social.handle');
Route::get('/social/{provider}/instructor/registration', [SocialController::class, 'getSocialInstructorRegistration'])
    ->name('social.instructor.registration')
    ->middleware(['guest']);
Route::get('/social/ig/load-media', [SocialController::class, 'getSocialInstagramMedia'])
    ->name('social.instagram.media.update')
    ->middleware(['role:Instructor']);
Route::post('/social/detach/{provider}', [SocialController::class, 'detachSocial'])
    ->middleware(['auth'])
    ->name('social.detach');

Route::get('/globalshop', [GlobalShopController::class, 'index'])->name('globalshop');
Route::get('/checkout', [CheckoutController::class, 'index' ])->name('checkout');

Route::get('/cart', [CartController::class, 'index'])->name('cart');

Route::prefix('instructor')->middleware('role:Instructor')->group( function () {
	Route::get('/dashboard', [InstructorDashboardController::class,'index'])->name('instructor.dashboard');
	Route::get('/schedule', [InstructorScheduleController::class,'index'])->name('instructor.schedule');
	Route::get('/bookings', [InstructorBookingsController::class,'index'])->name('instructor.bookings');
	Route::get('/clients', [InstructorClientsController::class,'index'])->name('instructor.clients');
	Route::get('/gallery', [InstructorGalleryController::class,'index'])->name('instructor.gallery');
	Route::get('/incomes', [InstructorIncomesController::class,'index'])->name('instructor.incomes');
	Route::get('/payouts', [InstructorPayoutsController::class,'index'])->name('instructor.payouts');
	Route::get('/invite-instructor', [InstructorInvitesController::class,'index'])->name('instructor.invites');
	Route::get('/my-shop', [InstructorMyShopController::class,'index'])->name('instructor.my-shop');
	Route::get('/my-shop/video/{video}', [InstructorMyShopVideoController::class,'index'])->name('instructor.my-shop.video');
	Route::get('/discount-management', [InstructorDiscountManagementController::class,'index'])->name('instructor.discount-management');
});

Route::prefix('student')->middleware('role:Student')->group(function () {
	Route::get('/dashboard', [StudentDashboardController::class,'index'])->name('student.dashboard');
	Route::get('/schedule', [StudentScheduleController::class,'index'])->name('student.schedule');
	Route::get('/bookings', [StudentBookingsController::class,'index'])->name('student.bookings');
	Route::get('/instructors', [StudentInstructorsController::class,'index'])->name('student.instructors');
	Route::get('/gallery', [StudentGalleryController::class,'index'])->name('student.gallery');
	Route::get('/library', [StudentLibraryController::class,'index'])->name('student.library');
	Route::get('/library/video/{video}', [StudentLibraryVideoController::class,'index'])->name('student.library.video');
});

// Virtual lessons
Route::post('lesson/room-events', [VirtualLessonRoomEventsController::class,'handle'])->name('twiliocallbackhandler')->middleware(['guest']);
// open videochat window
Route::get('/virtual-lesson/{lesson}', [VirtualLessonPageController::class,'index'])
    ->name('virtual-lesson')
    ->middleware(['role:Instructor|Student']);


// BACKEND ROUTES
Route::prefix('backend')->middleware(['rememberHttpReferer', 'role:Admin'])->group( function () {
	Route::post('/login-as', [LoginAsController::class,'loginAs']);
	Route::get('/', [DashboardController::class,'index'])->name('backend.dashboard');
    Route::resource('genres', GenreController::class)->names('backend.genres');
    Route::resource('/genre-categories', GenreCategoryController::class )->names('backend.genre-categories');
    Route::resource('/faqs', FaqController::class)->names('backend.faqs');
    Route::resource('/faq-categories', FaqCategoryController::class)->names('backend.faq-categories');
	Route::get('lessons', [LessonController::class,'index'])->name('backend.lessons.index');
    Route::resource('/testimonials', TestimonialController::class)->names('backend.testimonials');
	Route::get('/instructors', [InstructorController::class,'index'])->name('backend.instructors.index');
	Route::get('/students', [StudentController::class,'index'])->name('backend.students.index');

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

//Route::group(['middleware' => ['role:Admin']], function () {
//	Route::get('/laravel-filemanager', '\UniSharp\LaravelFilemanager\Controllers\LfmController@show');
//	Route::post('/laravel-filemanager/upload', '\UniSharp\LaravelFilemanager\Controllers\UploadController@upload');
//	// list all lfm routes here...
//});

Route::any('{all}', 'PageController@index')->where('all', '.*');
