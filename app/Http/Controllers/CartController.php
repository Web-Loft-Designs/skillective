<?php

namespace App\Http\Controllers;

use App\Repositories\CartRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * @param CartRepository $cartRepository
     * @return Application|Factory|View
     */
    public function index(CartRepository $cartRepository)
    {
        $user = Auth::user();

        $total = 0;
        if($user){
            $total = $cartRepository->getCartSummary($user->id, null, "[]");
        }
        $vars = [
            'page_title'	=> 'Cart',
            'total' => $total
        ];

        return view('frontend.cart', $vars);
    }

}
