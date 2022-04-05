<?php

namespace App\Http\Controllers;
use App\Repositories\CartRepository;
use Auth;

class CartController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
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
