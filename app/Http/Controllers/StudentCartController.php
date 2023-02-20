<?php

namespace App\Http\Controllers;
use App\Repositories\CartRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class StudentCartController extends Controller
{

    /**
     * @param CartRepository $cartRepository
     * @return Application|Factory|View
     */
    public function index(CartRepository $cartRepository)
    {
        // TODO Bag

        $total = $cartRepository->getCartSummary();
        $vars = [
            'page_title'	=> 'Cart',
            'total' => $total
        ];
        return view('frontend.student.cart', $vars);
    }

}
