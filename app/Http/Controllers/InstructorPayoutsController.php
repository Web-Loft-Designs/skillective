<?php

namespace App\Http\Controllers;

use Auth;
use Session;
use Log;
use App\Facades\BraintreeProcessor;
use Prettus\Repository\Presenter\ModelFractalPresenter;
use Illuminate\Http\Request;
use App\Repositories\BookingRepository;
use App\Repositories\GenreRepository;

class InstructorPayoutsController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
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
