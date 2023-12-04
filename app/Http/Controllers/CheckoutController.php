<?php

namespace App\Http\Controllers;

use App\Facades\PayPalProcessor;
use App\Repositories\CartRepository;
use App\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param CartRepository $cartRepository
     * @param UserRepository $userRepository
     * @return View
     */
    public function index(CartRepository $cartRepository, UserRepository $userRepository): View
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
            'page_title'        => 'Checkout',
            'total'             => $total,
            'lessonsCount'      => $lessonsInACart,
            'user'              => $userData,
        ];

        $isStudent = ($user && $user->hasRole(User::ROLE_STUDENT));
        if (!$user || $isStudent) {
            $vars['ppClientToken'] = PayPalProcessor::getClientId();
            $vars['bnCode'] = PayPalProcessor::getBnCde();
            $vars['ppUserPaymentMethods'] = $isStudent ? PayPalProcessor::getSavedCustomerPaymentMethods($user) : [];
            $vars['masterMerchantId'] = PayPalProcessor::getMasterMerchantId();
            $vars['dataUserIdToken'] = PayPalProcessor::getDataUserIdToken($user);

        }

        return view('frontend.checkout', $vars);
    }
}
