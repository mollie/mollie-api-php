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
        $includePayment = $this->queryIncludes('include', PaymentIncludesQuery::PAYMENT);

        return new GetPaymentCaptureRequest(
            $this->paymentId,
            $this->captureId,
            $this->query('includePayment', $includePayment),
        );
    }
}
