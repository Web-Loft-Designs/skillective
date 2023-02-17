<?php

namespace App\Http\Controllers;

use App\Repositories\GenreRepository;
use App\Repositories\LessonRepository;
use App\Repositories\UserRepository;
use App\Models\Lesson;
use App\Models\User;
use App\Facades\BraintreeProcessor;
use Braintree\MerchantAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LessonPageController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Lesson $lesson, GenreRepository $genreRepository, UserRepository $userRepository)
    {
    	if ($lesson->is_cancelled){
    		abort(404, 'Lesson cancelled');
		}

		if (config('app.env')=='prod'
			&& (
				$lesson->instructor->bt_submerchant_id==null
				|| $lesson->instructor->bt_submerchant_status!=MerchantAccount::STATUS_ACTIVE
				|| $lesson->instructor->status != User::STATUS_ACTIVE
			)
			&& (!Auth::user() || Auth::user()->id!=$lesson->instructor_id)
		){
			abort(404, 'Lesson not found');
		}


		$userData = null;
		$userPaymentMethods = [];
		$user = Auth::user();
    	if ($user){
			$userData = $userRepository->getUserData(Auth::user()->id);
			$userData = $userRepository->presentResponse($userData)['data'];
			$userData['genres'] = Auth::user()->genres()->pluck('id')->toArray();
		}

    	$vars = [
			'page_title' 		=> 'Book Lesson',
    		'lesson'			=> $lesson,//$lessonRepository->presentResponse($lesson),
			'user'				=> $userData,
			'siteGenres'		=> $genreRepository->presentResponse($genreRepository->getSiteGenres())['data'],
			'categorizedGenres' => $genreRepository->getCategorizedGenres(),
		];

		$isStudent = ($user && $user->hasRole(User::ROLE_STUDENT));
		if (!$user || $isStudent){
			$vars['clientToken'] = BraintreeProcessor::generateClientToken($user);
			$vars['paymentEnvironment'] = config('services.braintree.environment');
			$vars['userPaymentMethods'] = $isStudent ? BraintreeProcessor::getSavedCustomerPaymentMethods($user) : [];
		}

        return view('frontend.lesson', $vars);
    }

	public function venmoTest(Lesson $lesson, LessonRepository $lessonRepository, GenreRepository $genreRepository, UserRepository $userRepository)
	{
		Log::debug('venmoTest');
		if ($lesson->is_cancelled){
			Log::debug('Lesson cancelled');
			abort(404, 'Lesson cancelled');
		}
		if ((
				$lesson->instructor->bt_submerchant_id==null
				|| $lesson->instructor->bt_submerchant_status!=MerchantAccount::STATUS_ACTIVE
				|| $lesson->instructor->status != User::STATUS_ACTIVE
			)
			&& (!Auth::user() || Auth::user()->id!=$lesson->instructor_id)
		){
			Log::debug('Lesson not found');
			abort(404, 'Lesson not found');
		}

		$userData = null;
		$user = Auth::user();
		if ($user){
			$userData = $userRepository->getUserData(Auth::user()->id);
			$userData = $userRepository->presentResponse($userData)['data'];
			$userData['genres'] = Auth::user()->genres()->pluck('id')->toArray();
		}

		$vars = [
			'page_title' 		=> 'Book Lesson',
			'lesson'			=> $lesson,
			'user'				=> $userData
		];

		$isStudent = ($user && $user->hasRole(User::ROLE_STUDENT));
		if (!$user || $isStudent){
			$vars['clientToken'] = BraintreeProcessor::generateClientToken($user);
			$vars['paymentEnvironment'] = config('services.braintree.environment');
		}

		return view('frontend.venmoTest', $vars);
	}
}
