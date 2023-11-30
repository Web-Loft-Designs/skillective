<?php

namespace Srmklive\PayPal\Traits;

use Srmklive\PayPal\Services\PayPal;

trait PayPalExperienceContext
{
    /**
     * @var array
     */
    protected $experience_context = [];

    /**
     * Set Brand Name when setting experience context for payment.
     *
     * @param string $brand
     *
     * @return PayPal
     */
    public function setBrandName(string $brand): PayPal
    {
        $this->experience_context = array_merge($this->experience_context, [
            'brand_name' => $brand,
        ]);

        return $this;
    }

    /**
     * Set return & cancel urls.
     *
     * @param string $return_url
     * @param string $cancel_url
     *
     * @return PayPal
     */
    public function setReturnAndCancelUrl(string $return_url, string $cancel_url): PayPal
    {
        $this->experience_context = array_merge($this->experience_context, [
            'return_url' => $return_url,
            'cancel_url' => $cancel_url,
        ]);

        return $this;
    }

    /**
     * Set stored payment source.
     *
     * @param string      $initiator
     * @param string      $type
     * @param string      $usage
     * @param bool        $previous_reference
     * @param string|null $previous_transaction_id
     * @param string|null $previous_transaction_date
     * @param string|null $previous_transaction_reference_number
     * @param string|null $previous_transaction_network
     *
     * @return PayPal
     */
    public function setStoredPaymentSource(string $initiator, string $type, string $usage, bool $previous_reference = false, string $previous_transaction_id = null, string $previous_transaction_date = null, string $previous_transaction_reference_number = null, string $previous_transaction_network = null): PayPal
    {
        $this->experience_context = array_merge($this->experience_context, [
            'stored_payment_source' => [
                'payment_initiator' => $initiator,
                'payment_type'      => $type,
                'usage'             => $usage,
            ],
        ]);

        if ($previous_reference === true) {
            $this->experience_context['stored_payment_source']['previous_network_transaction_reference'] = [
                'id'                        => $previous_transaction_id,
                'date'                      => $previous_transaction_date,
                'acquirer_reference_number' => $previous_transaction_reference_number,
                'network'                   => $previous_transaction_network,
            ];
        }

        return $this;
    }
}
