<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CheckoutRequest;
use Illuminate\Http\Request;
use App\Models\PromoCode;
use App\Models\Cart;
use App\Models\Lesson;
use App\Models\PreRecordedLesson;
use App\Models\Booking;
use App\Models\PurchasedLesson;
use App\Repositories\UserRepository;
use App\Repositories\CartRepository;
use Illuminate\Support\Facades\App;
use App\Facades\UserRegistrator;
use App\Http\Requests\API\StudentRegisterRequest;
use Auth;
use Log;
use Carbon\Carbon;


class CartAPIController extends AppBaseController
{

    private $cartRepository;

    public function __construct(cartRepository $cartRepo)
    {
        $this->cartRepository = $cartRepo;
    }


    public function index(Request $request)
    {

        $student_id = null;

        if (Auth::user()) {
            $student_id = Auth::user()->id;
        }

        $guest_cart = $request->query('guest_cart');


        $cart = $this->cartRepository->getUserCart($student_id, $guest_cart);


        $this->cartRepository->setPresenter("App\\Presenters\\CartListPresenter");
        $cart = $this->cartRepository->presentResponse($cart);
        return  $this->sendResponse($cart);
    }

    public function isCartHasItems()
    {
        $student_id = null;

        if (Auth::user()) {
            $student_id = Auth::user()->id;
        }

        $cartItems = $this->cartRepository->getUserCart($student_id, null);

        if (count($cartItems) > 0) {
            return  $this->sendResponse(true);
        } else {
            return  $this->sendResponse(false);
        }
    }


    public function getCartSummary(Request $request)
    {
        $student_id = null;

        if (Auth::user()) {
            $student_id = Auth::user()->id;
        }

        $guest_cart = $request->query('guest_cart');
        $promo_codes = $request->query('promo_codes');

        $response = $this->cartRepository->getCartSummary($student_id, $guest_cart, $promo_codes);

        return  $this->sendResponse($response);
    }

    public function checkIsPromoIsValid(Request $request, $promo)
    {
        $promo = PromoCode::where('name', '=', $promo)->first();

        if (empty($promo)) {
            return $this->sendError('Promo Code not found');
        }

        $nowOnServer = Carbon::now();

        if($nowOnServer->greaterThan($promo->finish)){
            return $this->sendError('Promo Code expiried');
        }
        return  $this->sendResponse('Promo Code Valid');
    }


    private function _checkForLessonAvailability()
    {
        // if ( $lesson->getCountFreeSpots()==0 ){
        // 	return 'No free spots left';
        // }
        // if ($lesson->private_for_student_id && $lesson->private_for_student_id!=Auth::id()){
        //     return 'You can\'t book this Private Lesson';
        // }
        // if ( $lesson->is_cancelled ){
        // 	return 'Lesson cancelled';
        // }
        // if ($lesson->alreadyStarted()){
        // 	return 'Lesson already started. Booking disabled.';
        // }

        // if (!currentUserCanBook()) {
        //     return 'You have no permissions to book lessons';
        // }
        // any other reasons with same error message
        // if (!$lesson->isBookableNowByCurrentUser()){
        // 	return 'Lesson can\'t be booked';
        // }

        // if (config('app.env')=='prod'
        // 	&& (
        // 		$lesson->instructor->bt_submerchant_id==null
        // 		|| $lesson->instructor->bt_submerchant_status!=\Braintree_MerchantAccount::STATUS_ACTIVE
        // 		|| $lesson->instructor->status != User::STATUS_ACTIVE
        // 	)
        // ){
        // 		return 'Instructor not active or doesn\'t have a merchant account';
        // }
        return '';
    }


    public function checkout(Request $request, UserRepository $user_repository)
    {

        $student = null;

        $guest_cart = $request->input('guest_cart');
        $promos = $request->input('promo_codes', []);

        $cart = [];

        if (Auth::user() == null) {
            $input = $request->except(['payment_method_token', 'payment_method_nonce']);
            $srr = new CheckoutRequest($input);
            $student = UserRegistrator::registerInactiveStudent($srr);
            $cart = $this->cartRepository->getUserCart(null, json_encode($guest_cart));
        } else {
            $student = Auth::user();
            $cart = $this->cartRepository->getUserCart($student->id, null);
        }


        $promo_codes = [];

        foreach($promos as $key => $promo){
            $promo = PromoCode::where('name', '=', $promo)->first();

            if (empty($promo)) {
               continue;
            }

            $promo_codes[$promo->id] = $promo;
        }

        $cartCount = count($cart);

        $nonce = $request->input('payment_method_nonce');

        $appendedGenres = array();

        $discounts = [];

        foreach ($cart as $key => $cartItem) {

            if ($cartItem->discounts) {
                foreach ($cartItem->discounts as $discountKey => $discount) {
                    if ($discount->isActivate) {
                        $discounts[$discount->id] = $discount;
                    }
                }
            }
        }

        $cartCount = count($cart);

        foreach ($cart as $key => $cartItem) {
            if ($cartItem->lesson_id) {
                array_push($appendedGenres, $cartItem->lesson->genre_id);

                $lesson = new Lesson(json_decode(json_encode($cartItem->lesson), true));
                $request->request->add(['lesson_id' => $cartItem->lesson->id]);

                foreach ($discounts as $discountKey => $discount) {
                    if ($discount->lesson_type == "all" || $discount->lesson_type == 'virtual' && $lesson->lesson_type == 'virtual' || $discount->lesson_type == 'in-person' && $lesson->lesson_type == 'in_person') {

                        if ($discount->discount_type == 'fixed-amount') {
                            $discountAmount = $discount->discount / $cartCount;
                            $lesson->spot_price -= $discountAmount;
                        } else if ($discount->discount_type == 'percent') {
                            $discountAmount = $discount->discount / 100 * $lesson->spot_price;
                            $lesson->spot_price -= $discountAmount;
                        }
                    }
                }

                foreach ($promo_codes as $discountKey => $promo) {
                    if ($promo->lesson_type == "all" || $promo->lesson_type == 'virtual' && $lesson->lesson_type == 'virtual' || $promo->lesson_type == 'in-person' && $lesson->lesson_type == 'in_person') {

                        if ($promo->discount_type == 'fixed-amount') {
                            $discountAmount = $promo->discount / $cartCount;
                            $lesson->spot_price -= $discountAmount;
                        } else if ($discount->discount_type == 'percent') {
                            $discountAmount = $promo->discount / 100 * $lesson->spot_price;
                            $lesson->spot_price -= $discountAmount;
                        }
                    }
                }

                $lesson->book($user_repository, $request, $nonce ? $nonce[$key] : "", $student);
            } else {
                array_push($appendedGenres, $cartItem->preRecordedLesson->genre_id);

                $preRLesson = new PreRecordedLesson(json_decode(json_encode($cartItem->preRecordedLesson), true));
                $request->request->add(['pre_r_lesson_id' => $cartItem->preRecordedLesson->id]);



                foreach($discounts as $discountKey => $discount){
                    if($discount->lesson_type == "all" || $discount->lesson_type == 'pre-recorded'){
                        if($discount->discount_type == 'fixed-amount'){
                            $discountAmount = $discount->discount / $cartCount;
                            $preRLesson->price -= $discountAmount;
                        }
                        else if($discount->discount_type == 'percent'){
                            $discountAmount = $discount->discount / 100 * $preRLesson->price;
                            $preRLesson->price -= $discountAmount;
                        }
                    }
                }

                foreach($promo_codes as $discountKey => $promo){
                    if($promo->lesson_type == "all" || $promo->lesson_type == 'pre-recorded'){
                        if($promo->discount_type == 'fixed-amount'){
                            $discountAmount = $promo->discount / $cartCount;
                            $preRLesson->price -= $discountAmount;
                        }
                        else if($promo->discount_type == 'percent'){
                            $discountAmount = $promo->discount / 100 * $preRLesson->price;
                            $preRLesson->price -= $discountAmount;
                        }
                    }
                }

                $preRLesson->purchareLesson($user_repository, $request, $nonce ? $nonce[$key] : "", $student);
            }

            if (Auth::user() != null) {
                $cartItem->delete();
            }
        }


        if (Auth::user() != null) {
            $user_repository->appendGenres($cartItem->student_id, $appendedGenres);
        }

        return $this->sendResponse(true);
    }


    public function validateUserData(Request $request)
    {
        // Log::info('validateUserData:');
        // Log::info($request);

        if (($error = $this->_checkForLessonAvailability()) != '') {
            return $this->sendError($error, 400);
        }

        $settingsModel = App::make('App\Models\Setting');
        $settingsModel->incrementValue('report_count_payment_form_views');

        return $this->sendResponse(true, 'User data valid');
    }

    public function store(Request $request)
    {
        $isPreRecorded = $request->input('isPreRecorded');

        if ($isPreRecorded) {
            $isExist = Cart::where('pre_r_lesson_id', $request->input('lesson_id'))->where('student_id', Auth::user()->id)->first();

            if ($isExist) {
                return $this->sendError('Product already in cart', 400);
            }

            $lessonAlreadyPurchased = PurchasedLesson::where('pre_r_lesson_id',  $request->input('lesson_id'))->where('student_id', Auth::user()->id)->first();


            if ($lessonAlreadyPurchased) {
                return $this->sendError('You already purchased this lesson', 400);
            }


            $data = array();

            $data['student_id'] = Auth::user()->id;
            $data['pre_r_lesson_id'] = $request->input('lesson_id');

            $lesson = PreRecordedLesson::where('id',  $request->input('lesson_id'))->first();

            $data['instructor_id'] = $lesson->instructor_id;
            $data['description'] = $request->input('description');

            $result = Cart::create($data);
        } else {
            $isExist = Cart::where('lesson_id', $request->input('lesson_id'))->where('student_id', Auth::user()->id)->first();

            if ($isExist) {
                return $this->sendError('Product already in cart', 400);
            }

            $data = array();

            $data['student_id'] = Auth::user()->id;
            $data['lesson_id'] = $request->input('lesson_id');

            $lesson = Lesson::where('id',  $request->input('lesson_id'))->first();

            if ($lesson->getCountFreeSpots() == 0) {
                return $this->sendError('No free spots left', 400);
            }

            $booking = Booking::join('lessons', 'lesson_id', '=', "lessons.id")
                ->where('lessons.id', $request->input('lesson_id'))
                ->where('student_id', Auth::user()->id)
                ->whereRaw(" ( bookings.status <> 'cancelled' OR bookings.status IS NULL ) ")
                ->first();


            if ($booking) {
                return $this->sendError('You allready book this lesson', 400);
            }

            $data['instructor_id'] = $lesson->instructor_id;
            $data['description'] = $request->input('description');

            $result = Cart::create($data);

            return  $this->sendResponse($result);
        }
    }

    public function delete(Cart $cart)
    {
        $student_id = Auth::user()->id;

        $cartItem = Cart::where('id', $cart->id)->where('student_id', $student_id)->first();

        if (!$cartItem) {
            return $this->sendError("Cart item didn't found in cart", 400);
        }

        $deletedCartItem = $cartItem->delete();

        return  $this->sendResponse($deletedCartItem);
    }
}
