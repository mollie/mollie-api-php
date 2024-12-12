<?php

namespace Mollie\Api\Http\Data;

use Mollie\Api\Types\PaymentIncludesQuery;
use Mollie\Api\Utils\Arr;

class GetPaymentCaptureQuery extends Data
{
    public bool $includePayment = false;

    public function __construct(
        bool $includePayment = false
    ) {
        $this->includePayment = $includePayment;
    }

    public function toArray(): array
    {
        return [
            'include' => Arr::join($this->includePayment ? [PaymentIncludesQuery::PAYMENT] : []),
        ];
    }
}
