<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\GetPaginatedPaymentRefundsRequest;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaginatedPaymentRefundsRequestFactory extends RequestFactory
{
    private string $paymentId;

    public function __construct(string $paymentId)
    {
        $this->paymentId = $paymentId;
    }

    public function create(): GetPaginatedPaymentRefundsRequest
    {
        $includePayment = $this->queryIncludes('include', PaymentIncludesQuery::PAYMENT);

        return new GetPaginatedPaymentRefundsRequest(
            $this->paymentId,
            $this->query('from'),
            $this->query('limit'),
            $this->query('includePayment', $includePayment),
        );
    }
}
