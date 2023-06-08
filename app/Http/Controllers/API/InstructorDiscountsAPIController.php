<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateDiscountAPIRequest;
use App\Http\Requests\API\CreatePromoCodeAPIRequest;
use App\Notifications\StudentPromoNotification;
use App\Models\Discount;
use App\Models\PromoCode;
use App\Models\User;
use App\Repositories\DiscountRepository;
use App\Repositories\PromoCodeRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Prettus\Validator\Exceptions\ValidatorException;

class InstructorDiscountsAPIController extends AppBaseController
{

    private $discountRepository;
    private $promoCodeRepository;

    public function __construct(DiscountRepository $discountRepo, PromoCodeRepository $promoRepo)
    {
        parent::__construct();
        $this->discountRepository = $discountRepo;
        $this->promoCodeRepository = $promoRepo;
    }


    /**
     * @param $id
     * @return JsonResponse
     */
    public function indexDiscounts($id)
    {
        $discounts = $this->discountRepository->getInstructorsDiscounts($id);

        return  $this->sendResponse($discounts);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function indexPromoCodes($id)
    {
        $promos = $this->promoCodeRepository->getInstructorPromos($id);

        return  $this->sendResponse($promos);
    }


    /**
     * @param CreateDiscountAPIRequest $request
     * @return JsonResponse
     */
    public function storeDiscount(CreateDiscountAPIRequest $request)
    {
        $data = array();
        $data['instructor_id'] = Auth::user()->id;
        $data['title'] = $request->input('title');
        $data['lesson_type'] = $request->input('lesson_type');
        $data['discount_type'] = $request->input('discount_type');
        $data['discount'] = $request->input('discount');
        $data['lessons_for_apply'] = $request->input('lessons_for_apply');
        $data['start'] = $request->input('start');
        $data['finish'] = $request->input('finish');
        $data['users_count'] = $request->input('users_count');
        $data['used_time'] = 0;
        $data['used_with_other_discounts'] = $request->input('used_with_other_discounts');

        $created = Discount::create($data);

        return $this->sendResponse($created);
    }

    /**
     * @param CreatePromoCodeAPIRequest $request
     * @return JsonResponse
     */
    public function storePromoCode(CreatePromoCodeAPIRequest $request)
    {
        $data = array();
        $data['instructor_id'] = Auth::user()->id;
        $data['name'] = $request->input('name');
        $data['title'] = $request->input('title');
        $data['lesson_type'] = $request->input('lesson_type');
        $data['discount_type'] = $request->input('discount_type');
        $data['discount'] = $request->input('discount');
        $data['start'] = $request->input('start');
        $data['finish'] = $request->input('finish');
        $data['users_count'] = $request->input('users_count');
        $data['used_time'] = 0;
        $data['used_with_other_discounts'] = $request->input('used_with_other_discounts');
        $notifyClients = $request->input('notifyClients');
        $created = PromoCode::create($data);

        foreach ($notifyClients as $key => $client) {
            $student = User::find($client);

            if (!$student) {
                continue;
            }

            $student->notify(new StudentPromoNotification($created));
        }

        return $this->sendResponse($created);
    }

    /**
     * @param CreateDiscountAPIRequest $request
     * @param $discount
     * @return JsonResponse|void
     * @throws ValidatorException
     */
    public function updateDiscount(CreateDiscountAPIRequest $request, $discount)
    {
        $discountItem = $this->discountRepository->findWithoutFail($discount);
        if (empty($discountItem)) {
            return $this->sendError('Discount not found');
        }
        $data = $request->toArray();
        $discountItem = $this->discountRepository->update($data, $discountItem['id']);
        $this->sendResponse("Discount Updated");
    }

    /**
     * @param CreatePromoCodeAPIRequest $request
     * @param $promo
     * @return JsonResponse|void
     * @throws ValidatorException
     */
    public function updatePromo(CreatePromoCodeAPIRequest $request, $promo)
    {
        $promo = $this->promoCodeRepository->findWithoutFail($promo);

        if (empty($promo)) {
            return $this->sendError('Promo Code not found');
        }

        $data = $request->toArray();

        $promo = $this->promoCodeRepository->update($data, $promo['id']);

        $notifyClients = $request->input('notifyClients');

        foreach ($notifyClients as $key => $client) {
            $student = User::find($client);

            if (!$student) {
                continue;
            }

            $student->notify(new StudentPromoNotification($promo));
        }


        $this->sendResponse("Promo Code Updated");
    }

    /**
     * @param $discount
     * @return JsonResponse
     */
    public function deleteDiscount($discount)
    {
        $discountItem = Discount::where('id', $discount)->first();

        $instructorId = Auth::user()->id;

        if (!$discountItem) {
            return $this->sendError("Discount not found", 400);
        }

        if ($instructorId != $discountItem->instructor_id) {
            return $this->sendError("You dont have permissions to do this", 400);
        }

        $deletedDiscountItem = $discountItem->delete();

        return  $this->sendResponse($deletedDiscountItem);
    }


    /**
     * @param $promoCode
     * @return JsonResponse
     */
    public function deletePromo($promoCode)
    {

        $promoItem = PromoCode::where('id', $promoCode)->first();

        $instructorId = Auth::user()->id;

        if (!$promoItem) {
            return $this->sendError("Promo code not found", 400);
        }

        if ($instructorId != $promoItem->instructor_id) {
            return $this->sendError("You dont have permissions to do this", 400);
        }

        $deletedItem = $promoItem->delete();

        return  $this->sendResponse($deletedItem);
    }
}
