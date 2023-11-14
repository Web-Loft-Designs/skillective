<?php

namespace App\Http\Controllers;

use App\Repositories\CartRepository;
use App\Repositories\GenreRepository;
use App\Repositories\UserRepository;
use App\Facades\BraintreeProcessor;
use App\Models\User;
use App\Services\PayPalProcessor;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    private UserRepository $userRepository;
    private PayPalProcessor $payPalProcessor;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository	= $userRepo;
        $this->payPalProcessor = new PayPalProcessor($userRepo);

        parent::__construct();
    }

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
            'page_title'        => 'Checkout',
            'total'             => $total,
            'lessonsCount'      => $lessonsInACart,
            'user'              => $userData,
        ];

        $isStudent = ($user && $user->hasRole(User::ROLE_STUDENT));
        if (!$user || $isStudent) {
            $vars['clientToken'] = BraintreeProcessor::generateClientToken($user);
            $vars['paymentEnvironment'] = config('services.braintree.environment');
            $vars['userPaymentMethods'] = $isStudent ? BraintreeProcessor::getSavedCustomerPaymentMethods($user) : [];

            $vars['ppMerchantId'] = PayPalProcessor::getMasterMerchatId();
            $vars['ppClientToken'] = PayPalProcessor::getClientId();
            $vars['ppPaymentEnvironment'] = PayPalProcessor::getEnvironment();
            $vars['ppUserPaymentMethods'] = [];

        }

        return view('frontend.checkout', $vars);
    }
}
