<?php

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Resolvable;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaymentChargebackQuery implements Resolvable
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
