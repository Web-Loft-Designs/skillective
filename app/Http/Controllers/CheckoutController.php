<?php

namespace App\Http\Controllers;

use App\Repositories\CartRepository;
use App\Repositories\GenreRepository;
use App\Repositories\UserRepository;
use App\Facades\BraintreeProcessor;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{

    /**
     * @param Request $request
     * @param CartRepository $cartRepository
     * @param GenreRepository $genreRepository
     * @param UserRepository $userRepository
     * @return Application|Factory|View
     */
    public function index(Request $request, CartRepository $cartRepository, GenreRepository $genreRepository, UserRepository $userRepository)
    {
        $userData = null;
        $user = Auth::user();
        $total = 0;
        $lessonsInACart = [];
        if ($user) {
            $userData = $userRepository->getUserData(Auth::user()->id);
            $userData = $userRepository->presentResponse($userData)['data'];
            $userData['genres'] = Auth::user()->genres()->pluck('id')->toArray();
            $total = $cartRepository->getCartSummary(Auth::user()->id, null, "[]");
            $lessonsInACart = $cartRepository->getLessonsCountInCart(Auth::user()->id, null);
        }

        $vars = [
            'page_title'    => 'Checkout',
            'total' => $total,
            'lessonsCount' => $lessonsInACart,
            'user'                => $userData,
            'siteGenres'        => $genreRepository->presentResponse($genreRepository->getSiteGenres())['data'],
            'categorizedGenres' => $genreRepository->getCategorizedGenres(),
        ];

        $isStudent = ($user && $user->hasRole(User::ROLE_STUDENT));
        if (!$user || $isStudent) {
            $vars['clientToken'] = BraintreeProcessor::generateClientToken($user);
            $vars['paymentEnvironment'] = config('services.braintree.environment');
            $vars['userPaymentMethods'] = $isStudent ? BraintreeProcessor::getSavedCustomerPaymentMethods($user) : [];
        }

        return view('frontend.checkout', $vars);
    }
}
