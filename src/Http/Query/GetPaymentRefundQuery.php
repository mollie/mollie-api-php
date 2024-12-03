<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaymentRefundQuery extends Query
{
    private bool $includePayment;

    public function __construct(
        bool $includePayment = false
    ) {
        $this->includePayment = $includePayment;
    }

    public function toArray(): array
    {
        return [
            'include' => $this->includePayment ? PaymentIncludesQuery::PAYMENT : null,
        ];
    }
}
