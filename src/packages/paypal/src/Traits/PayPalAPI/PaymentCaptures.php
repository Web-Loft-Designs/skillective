<?php

namespace Srmklive\PayPal\Traits\PayPalAPI;

use Throwable;

trait PaymentCaptures
{
    /**
     * Show details for a captured payment.
     *
     * @param string $capture_id
     *
     * @throws Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/payments/v2/#captures_get
     */
    public function showCapturedPaymentDetails(string $capture_id)
    {
        $this->apiEndPoint = "v2/payments/captures/{$capture_id}";

        $this->verb = 'get';

        return $this->doPayPalRequest();
    }

    /**
     * Refund a captured payment.
     *
     * @param string $capture_id
     * @param string $invoice_id
     * @param float  $amount
     * @param string $note
     *
     * @throws Throwable
     *
     * @return array|\Psr\Http\Message\StreamInterface|string
     *
     * @see https://developer.paypal.com/docs/api/payments/v2/#captures_refund
     */
    public function refundCapturedPayment(string $capture_id, string $invoice_id, float $amount, string $note)
    {
        $this->apiEndPoint = "v2/payments/captures/{$capture_id}/refund";

        $this->options['json'] = [
            'amount' => [
                'value'         => $amount,
                'currency_code' => $this->currency,
            ],
            'invoice_id'    => $invoice_id,
            'note_to_payer' => $note,
        ];

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }

    /**
     * @param string $capture_id
     * @return float|object|int|bool|array|string|null
     * @throws Throwable
     */
    public function refundAllCapturedPayment(string $capture_id): float|object|int|bool|array|string|null
    {
        $this->apiEndPoint = "v2/payments/captures/{$capture_id}/refund";

        $this->options['json'] = (object)[];

        $this->verb = 'post';

        return $this->doPayPalRequest();
    }
}
