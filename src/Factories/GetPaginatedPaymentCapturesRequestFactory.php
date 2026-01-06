<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\GetPaginatedPaymentCapturesRequest;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaginatedPaymentCapturesRequestFactory extends RequestFactory
{
    private string $paymentId;

    public function __construct(string $paymentId)
    {
        $this->paymentId = $paymentId;
    }

    public function create(): GetPaginatedPaymentCapturesRequest
    {
        // Legacy: historically this factory accepted `includePayments` directly; Mollie uses `include=payment`.
        $includePayments = $this->queryIncludes('include', PaymentIncludesQuery::PAYMENT);

        return new GetPaginatedPaymentCapturesRequest(
            $this->paymentId,
            $this->query('from'),
            $this->query('limit'),
            $this->query('includePayments', $includePayments),
        );
    }
}
