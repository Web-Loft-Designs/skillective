<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\PurchasedLesson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use InfyOm\Generator\Common\BaseRepository;
use App\Models\Booking;
use App\Models\Lesson;
use App\Models\PreRecordedLesson;
use Carbon\Carbon;
use App\Models\Discount;
use App\Models\PromoCode;

class CartRepository extends BaseRepository
{
    public function model()
    {
        return Cart::class;
    }

    protected $skipPresenter = true;

    public function presenter()
    {
        return "Prettus\\Repository\\Presenter\\ModelFractalPresenter";
    }

    public function getUserCart($student_id, $product_list)
    {

        if ($student_id) {
            $this->scopeQuery(function ($query) use ($student_id) {
                $query
                    ->select('cart.*')
                    ->where('cart.student_id', $student_id)
                    ->join('lessons', 'cart.lesson_id', '=', "lessons.id")
                    ->leftJoin('bookings', function ($join) {
                        $join->on('lessons.id', '=', 'bookings.lesson_id')
                            ->whereRaw(" ( bookings.status <> 'cancelled' OR bookings.status IS NULL ) ");
                    })->groupBy('cart.id');

                return $query;
            })->with(['lesson', 'lesson.instructor', 'lesson.instructor.discounts' => function ($q) {
                $nowOnServer = Carbon::now()->format('Y-m-d H:i:s'); // UTC
                $q->whereRaw("'$nowOnServer' < discounts.finish");
            }]);

            $carts = $this->get(['cart.*', 'count_booked']);

            $this->resetScope();

            $this->scopeQuery(function ($query) use ($student_id) {
                $query
                    ->select('cart.*')
                    ->where('cart.student_id', $student_id)
                    ->join('pre_r_lessons', 'cart.pre_r_lesson_id', '=', "pre_r_lessons.id")
                    ->groupBy('cart.id');

                return $query;
            })->with(['preRecordedLesson', 'preRecordedLesson.instructor', 'preRecordedLesson.instructor.discounts' => function ($q) {
                $nowOnServer = Carbon::now()->format('Y-m-d H:i:s'); // UTC
                $q->whereRaw("'$nowOnServer' < discounts.finish");
            }]);

            $preRCart = $this->get();

            $carts = $carts->merge($preRCart);

            foreach ($carts as $index => $cartItem) {

                if ($cartItem->lesson_id) {
                    if (!$cartItem->lesson || $cartItem->count_booked >= $cartItem->lesson->spots_count) {
                        unset($carts[$index]);
                    }
                }

                if ($cartItem->lesson_id) {
                    $cartItem->discounts = $cartItem->lesson->instructor->discounts;
                    $handledDiscounts = Discount::validateDiscount($cartItem->discounts, $carts);
                    $cartItem->discounts = $handledDiscounts;
                } else if ($cartItem->pre_r_lesson_id) {
                    $cartItem->discounts = $cartItem->preRecordedLesson->instructor->discounts;
                    $handledDiscounts = Discount::validateDiscount($cartItem->discounts, $carts);
                    $cartItem->discounts = $handledDiscounts;
                }
            }

            return $carts;

        } else if ($product_list) {

            return $this->addToCart($product_list);
        }
    }

    /**
     * @param $product_list
     * @return mixed
     */
    public function addToCart($product_list)
    {
        $lessons_ids = [];
        $pre_r_lessons_ids = [];

        foreach (json_decode($product_list) as $key => $value) {

            if (isset($value->isPreRecorded)) {
                if( isset($value->lesson_id) ) {
                    $pre_r_lessons_ids[] = (int)$value->lesson_id;
                }

            } else {
                $lessons_ids[] = (int)$value->lesson_id;
            }
        }

        $lessons = Lesson::whereIn('lessons.id', $lessons_ids)
            ->leftJoin('bookings', function ($join) {
                $join->on('lessons.id', '=', 'bookings.lesson_id')
                    ->whereRaw(" ( bookings.status <> 'cancelled' OR bookings.status IS NULL ) ");
            })->with(["genre", 'instructor', 'instructor.discounts']);

       $carts = $lessons->get(["lessons.*"]);

       $preRCart = PreRecordedLesson::whereIn("pre_r_lessons.id", $pre_r_lessons_ids)->with(["genre", "instructor"])->get(["pre_r_lessons.*"]);

        $carts = $carts->merge($preRCart);

        foreach ($carts as $key => $value) {
            if ($value instanceof Lesson) {
                $metaCartItem = [
                    "lesson_id" => $value->id,
                    "instructor_id" => $value->instructor_id,
                ];
                $carts[$key] = new Cart($metaCartItem);
                $carts[$key]->lesson()->associate($value);

            } else {
                $metaCartItem = [
                    "pre_r_lesson_id" => $value->id,
                    "instructor_id" => $value->instructor_id,
                ];

                $carts[$key] = new Cart($metaCartItem);
                $carts[$key]->lesson()->associate($value);
            }
        }

        foreach ($carts as $index => $cartItem) {
            if ($cartItem->lesson_id) {
                if (!$cartItem->lesson || $cartItem->count_booked >= $cartItem->lesson->spots_count) {
                    unset($carts[$index]);
                }
            }
            if ($cartItem->lesson_id) {
                $cartItem->discounts = $cartItem->lesson->instructor->discounts;
                $handledDiscounts = Discount::validateDiscount($cartItem->discounts, $carts);
                $cartItem->discounts = $handledDiscounts;
            } else if ($cartItem->pre_r_lesson_id) {
                $cartItem->discounts = $cartItem->preRecordedLesson->instructor->discounts;
                $handledDiscounts = Discount::validateDiscount($cartItem->discounts, $carts);
                $cartItem->discounts = $handledDiscounts;
            }
        }

        return $carts;
    }


    public function getLessonsCountInCart($student_id, $guest_cart)
    {
        $cart = $this->getUserCart($student_id, $guest_cart);
        return  count($cart);
    }


    public function getCartSummary($student_id, $guest_cart, $promos, $message = null)
    {
        $cart = $this->getUserCart($student_id, $guest_cart);
        $response =  array("count" => 0, "total" => 0, "subtotal" => 0, "fee" => 0, "discount" => 0);
        $discounts = [];
        $promo_codes = [];

        foreach (json_decode($promos) as $key => $promo) {
            $promo = PromoCode::where('name', '=', $promo)->first();
            if (empty($promo)) {
                continue;
            }
            $promo_codes[$promo->id] = $promo;
        }

        foreach ($cart as $key => $cartItem) {
            if ($cartItem->lesson->instructor->discounts) {
                foreach ($cartItem->lesson->instructor->discounts as $discountKey => $discount) {

                    if ($discount->isActivate) {
                        $discounts[$discount->id] = $discount;
                    }
                }
            }
        }

        $cartCount = count($cart);

        foreach ($cart as $key => $cartItem) {
            if ($cartItem->lesson_id) {
                $booking = new Booking();
                $virtual_fee = round($booking->getBookingVirtualFeeAmount($cartItem->lesson), 2);
                $finishPrice = $cartItem->lesson->spot_price;

                foreach ($discounts as $discountKey => $discount) {
                    if ($discount->lesson_type == "all" || $discount->lesson_type == 'virtual'
                        && $cartItem->lesson->lesson_type == 'virtual' || $discount->lesson_type == 'in-person'
                        && $cartItem->lesson->lesson_type == 'in_person') {

                        if ($discount->discount_type == 'fixed-amount') {
                            $discountAmount = $discount->discount / $cartCount;
                            $response["discount"] += $discountAmount;
                            $finishPrice -= round($discountAmount, 2);
                        } else if ($discount->discount_type == 'percent') {
                            $discountAmount = $discount->discount / 100 * $cartItem->lesson->spot_price;
                            $response["discount"] += $discountAmount;
                            $finishPrice -= round($discountAmount, 2);
                        }
                    }
                }

                foreach ($promo_codes as $promoKey => $promo) {
                    if ($promo->lesson_type == "all" || $promo->lesson_type == 'virtual'
                        && $cartItem->lesson->lesson_type == 'virtual' || $promo->lesson_type == 'in-person'
                        && $cartItem->lesson->lesson_type == 'in_person') {

                        if ($promo->discount_type == 'fixed-amount') {
                            $discountAmount = $promo->discount / $cartCount;
                            $response["discount"] += $discountAmount;
                            $finishPrice -= round($discountAmount, 2);
                        } else if ($promo->discount_type == 'percent') {
                            $discountAmount = $promo->discount / 100 * $cartItem->lesson->spot_price;
                            $response["discount"] += $discountAmount;
                            $finishPrice -= round($discountAmount, 2);
                        }
                    }
                }

                $service_fee = round($booking->getBookingServiceFeeAmount($finishPrice), 2);
                $processor_fee = round($booking->getBookingPaymentProcessingFeeAmount($finishPrice, $service_fee + $virtual_fee), 2);
                $response["count"] += 1;
                $response["subtotal"] += round($cartItem->lesson->spot_price, 2);
                $response["fee"] += $service_fee + $virtual_fee + $processor_fee;
                $response["total"] += round($finishPrice + $service_fee + $virtual_fee + $processor_fee, 2);
            } else {
                $preRecordedLesson = new PreRecordedLesson();
                $finishPrice = $cartItem->preRecordedLesson->price;

                foreach ($discounts as $discountKey => $discount) {
                    if ($discount->lesson_type == "all" || $discount->lesson_type == 'pre-recorded') {
                        if ($discount->discount_type == 'fixed-amount') {
                            $discountAmount = $discount->discount / $cartCount;
                            $response["discount"] += $discountAmount;
                            $finishPrice -= round($discountAmount, 2);
                        } else if ($discount->discount_type == 'percent') {
                            $discountAmount = $discount->discount / 100 * $cartItem->preRecordedLesson->price;
                            $response["discount"] += $discountAmount;
                            $finishPrice -= round($discountAmount, 2);
                        }
                    }
                }

                foreach ($promo_codes as $discountKey => $promo) {
                    if ($promo->lesson_type == "all" || $promo->lesson_type == 'pre-recorded') {
                        if ($promo->discount_type == 'fixed-amount') {
                            $discountAmount = $promo->discount / $cartCount;
                            $response["discount"] += $discountAmount;
                            $finishPrice -= round($discountAmount, 2);
                        } else if ($promo->discount_type == 'percent') {
                            $discountAmount = $promo->discount / 100 * $cartItem->preRecordedLesson->price;
                            $response["discount"] += $discountAmount;
                            $finishPrice -= round($discountAmount, 2);
                        }
                    }
                }

                $service_fee = round($preRecordedLesson->getPreRecordedLessonServiceFeeAmount($finishPrice), 2);
                $processor_fee = round($preRecordedLesson->getPreRecordedLessonPaymentProcessingFeeAmount($finishPrice, $service_fee), 2);


                $response["count"] += 1;
                $response["subtotal"] += round($cartItem->preRecordedLesson->price, 2);
                $response["fee"] += $service_fee + $processor_fee;
                $response["total"] += $finishPrice + $service_fee + $processor_fee;

            }
        }

        $response["message"] = $message;

        return  $response;
    }

    public function presentResponse($data)
    {
        return $this->presenter->present($data);
    }


    /**
     * @param Request $request
     * @return false|JsonResponse
     */
    public function storeGuestCart(Request $request)
    {

        if ( !\Cookie::has('guest_cart') || !Auth::check() ) return false;

        $guestCart = json_decode(\Cookie::get('guest_cart'));

        foreach ( $guestCart as $item ){

            $check = true;
            if( isset($item->isPreRecorded) && $item->isPreRecorded )
            {

                $less = Cart::where('student_id', Auth::user()->id)
                    ->where('pre_r_lesson_id', $item->lesson_id)
                    ->first();

                if( $less ) $check = false;

            }else{

                $less = Cart::where('student_id', Auth::user()->id)
                    ->where('lesson_id', $item->lesson_id)
                    ->first();

                if( $less ) $check = false;

            }

            if( $check )
            {

                $data = [];

                $data['student_id'] = Auth::user()->id;
                $data['lesson_id'] = (isset($item->isPreRecorded) && $item->isPreRecorded) ? null : $item->lesson_id;
                $data['pre_r_lesson_id'] = (isset($item->isPreRecorded) && $item->isPreRecorded) ? $item->lesson_id : null;

                $lesson = Lesson::where('id',  $item->lesson_id)->first();

                $data['instructor_id'] = $lesson->instructor_id;
                $data['description'] = isset($item->description) ? $item->description : '';
                $data['is_guest'] = 1;

                $lessonAlreadyPurchased = null;
                if($data['pre_r_lesson_id'])
                {
                    $lessonAlreadyPurchased = PurchasedLesson::where('pre_r_lesson_id',  $data['pre_r_lesson_id'])->where('student_id', Auth::user()->id)->first();
                }

                if(!$lessonAlreadyPurchased) $result = Cart::create($data);

            }else{
                session()->flash('guestCartCheck', 'We have removed some lessons from the basket, as they were already purchased by you earlier.');
            }

        }

        return response()->json([
            'status' => true
        ]);

    }
}
