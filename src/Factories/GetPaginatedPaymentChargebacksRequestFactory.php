<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\GetPaginatedPaymentChargebacksRequest;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaginatedPaymentChargebacksRequestFactory extends RequestFactory
{
    private string $paymentId;

    public function __construct(string $paymentId)
    {
        $this->paymentId = $paymentId;
    }

    public function create(): GetPaginatedPaymentChargebacksRequest
    {
        $includePayment = $this->queryIncludes('include', PaymentIncludesQuery::PAYMENT);

        return new GetPaginatedPaymentChargebacksRequest(
            $this->paymentId,
            $this->query('from'),
            $this->query('limit'),
            $this->query('includePayment', $includePayment),
        );
    }
}
