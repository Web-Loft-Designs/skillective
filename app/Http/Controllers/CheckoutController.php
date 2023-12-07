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

    private CartRepository $cartRepository;

    public function __construct(CartRepository $cartRepo)
    {
        parent::__construct();
        $this->cartRepository = $cartRepo;
    }


    /**
     * @param CartRepository $cartRepository
     * @param UserRepository $userRepository
     * @return View
     */
    public function index(CartRepository $cartRepository, UserRepository $userRepository): View
    {

        if (Auth::check() && Auth::user()->hasRole(User::ROLE_STUDENT)) {

            $userData = $userRepository->getUserData(Auth::user()->id);
            $userData = $userRepository->presentResponse($userData)['data'];
            $userData['genres'] = Auth::user()->genres()->pluck('id')->toArray();
            $total = $cartRepository->getCartSummary(Auth::user()->id, null, "[]");
            $lessonsInACart = $cartRepository->getLessonsCountInCart(Auth::user()->id, null);
            $vars['page_title']             = 'Checkout';
            $vars['total']                  = $total;
            $vars['lessonsCount']           = $lessonsInACart;
            $vars['user']                   = $userData;
            $vars['ppClientToken']          = PayPalProcessor::getClientId();
            $vars['bnCode']                 = PayPalProcessor::getBnCde();
            $vars['ppUserPaymentMethods']   = PayPalProcessor::getSavedCustomerPaymentMethods(Auth::user());
            $vars['masterMerchantId']       = PayPalProcessor::getMasterMerchantId();
            $vars['dataUserIdToken']        = PayPalProcessor::getDataUserIdToken(Auth::user());

        } else {
            $vars['page_title']             = 'Checkout';
            $vars['total']                  = null;
            $vars['lessonsCount']           = null;
            $vars['user']                   = null;
            $vars['ppClientToken']          = PayPalProcessor::getClientId();
            $vars['bnCode']                 = PayPalProcessor::getBnCde();
            $vars['ppUserPaymentMethods']   = null;
            $vars['masterMerchantId']       = PayPalProcessor::getMasterMerchantId();
            $vars['dataUserIdToken']        = null;
        }

        return view('frontend.checkout', $vars);
    }
}
