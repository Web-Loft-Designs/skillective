<?php

namespace App\Http\Controllers;

use App\Facades\BraintreeProcessor;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Repositories\BookingRepository;
use App\Repositories\GenreRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InstructorPayoutsController extends Controller
{
    /**
     * @param Request $request
     * @param BookingRepository $booking_repository
     * @param GenreRepository $genreRepository
     * @return Application|Factory|View
     */
    public function index(Request $request, BookingRepository $booking_repository, GenreRepository $genreRepository)
    {
		$booking_repository->setPresenter("App\\Presenters\\BookingPayoutPresenter");
		try{
			$payouts = $booking_repository->presentResponse($booking_repository->getInstructorPayouts(Auth::user()->id, $request));
		}catch (\Exception $e){
			Log::error('getInstructorPayouts : ' . $e->getMessage());
			$payouts = ['data'=>[]];
		}

		$vars = [
			'page_title' => 'Payouts',
			'payouts' => $payouts,
			'siteGenres'	=> $genreRepository->presentResponse($genreRepository->getSiteGenres())['data'],
            'userGenres'	=> $genreRepository->presentResponse(Auth::user()->genres)['data']
		];
		$vars['savedMerchantAccountDetails']  = BraintreeProcessor::getMerchantAccountDetails(Auth::user());

        return view('frontend.instructor.payouts', $vars);
    }

}
