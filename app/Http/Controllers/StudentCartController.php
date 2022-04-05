<?php

namespace App\Http\Controllers;
use App\Repositories\CartRepository;

class StudentCartController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CartRepository $cartRepository)
    {

        $total = $cartRepository->getCartSummary();
        
        $vars = [
            'page_title'	=> 'Cart',
            'total' => $total
        ];

        return view('frontend.student.cart', $vars);
    }

}
