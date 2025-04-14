<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\GetPaymentRefundRequest;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaymentRefundRequestFactory extends RequestFactory
{
    private string $paymentId;

    private string $refundId;

    public function __construct(string $paymentId, string $refundId)
    {
        $this->paymentId = $paymentId;
        $this->refundId = $refundId;
    }

    public function create(): GetPaymentRefundRequest
    {
        $includePayment = $this->queryIncludes('include', PaymentIncludesQuery::PAYMENT);

        return new GetPaymentRefundRequest(
            $this->paymentId,
            $this->refundId,
            $this->query('includePayment', $includePayment),
        );
    }
}
