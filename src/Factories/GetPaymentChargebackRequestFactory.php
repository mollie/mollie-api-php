<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\GetPaymentChargebackRequest;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaymentChargebackRequestFactory extends RequestFactory
{
    private string $paymentId;

    private string $chargebackId;

    public function __construct(string $paymentId, string $chargebackId)
    {
        $this->paymentId = $paymentId;
        $this->chargebackId = $chargebackId;
    }

    public function create(): GetPaymentChargebackRequest
    {
        // Legacy: historically this factory accepted `includePayment` directly; Mollie uses `include=payment`.
        $includePayment = $this->queryHas('includePayment')
            ? (bool) $this->query('includePayment')
            : $this->queryIncludes('include', PaymentIncludesQuery::PAYMENT);

        return new GetPaymentChargebackRequest(
            $this->paymentId,
            $this->chargebackId,
            $includePayment,
        );
    }
}
