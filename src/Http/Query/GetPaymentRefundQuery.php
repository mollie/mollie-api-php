<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Types\PaymentIncludesQuery;
use Mollie\Api\Contracts\Arrayable;

class GetPaymentRefundQuery implements Arrayable
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
