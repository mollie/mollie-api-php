<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\GetPaymentCaptureRequest;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaymentCaptureRequestFactory extends RequestFactory
{
    private string $paymentId;

    private string $captureId;

    public function __construct(string $paymentId, string $captureId)
    {
        $this->paymentId = $paymentId;
        $this->captureId = $captureId;
    }

    public function create(): GetPaymentCaptureRequest
    {
        // Legacy: historically this factory accepted `includePayment` directly; this endpoint uses `embed=payment`.
        $embedPayment = $this->queryHas('includePayment')
            ? (bool) $this->query('includePayment')
            : $this->queryIncludes('embed', PaymentIncludesQuery::PAYMENT);

        return new GetPaymentCaptureRequest(
            $this->paymentId,
            $this->captureId,
            $embedPayment,
        );
    }
}
