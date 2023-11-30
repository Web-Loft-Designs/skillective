<?php

namespace App\Http\Controllers\API;

use App\Facades\PayPalProcessor;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\API\PayPalTransactionRequest;
use Illuminate\Support\Facades\Auth;

class StudentPaymentMethodsAPIController extends AppBaseController
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $methods = PayPalProcessor::getSavedCustomerPaymentMethods($user);
        return $this->sendResponse($methods);
    }

    /**
     * @param PayPalTransactionRequest $request
     * @return JsonResponse
     */
    public function store(PayPalTransactionRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::user();

        try {
            PayPalProcessor::createPaymentMethod($user, $data['payment_method_nonce']);

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 400);
        }

        return $this->sendResponse(true, 'Payment method saved');
    }


    /**
     * @param $paymentMethodId
     * @return JsonResponse
     */
    public function delete($paymentMethodId): JsonResponse
    {
        $user = Auth::user();
        try {
            $method = $user->findPaymentMethod()->where('id', $paymentMethodId)->first();

            $result = PayPalProcessor::deletePaymentMethod($user->pp_customer_id, $method->payment_method_token);
            if ($result) {
                $method->delete();
                return $this->sendResponse(true, 'Payment method was deleted');
            } else {
                return $this->sendError('Can\'t delete payment method', 400);
            }

        } catch (\Exception $e) {
            return $this->sendError('Payment method not found');
        }

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getPpVaultSetupToken(Request $request): JsonResponse
    {
        $type = "";
        $request->whenFilled('method', function ($method) use (&$type) {
            $type = match ($method) {
                'paypal' => "paypal",
                'venmo' => 'venmo',
                default => "card",
            };
        });

        if (Auth::check()) {
            $token = PayPalProcessor::getVaultSetupToken(Auth::user(), $type);
            return response()->json(
                ['vaultSetupToken' => $token]
            );
        } else {
            return response()->json(['message' => "Error Unauthorized"], 403);
        }

    }

}
