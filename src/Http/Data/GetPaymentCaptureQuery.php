<?php

namespace Mollie\Api\Http\Data;

use Mollie\Api\Helpers\Arr;
use Mollie\Api\Types\PaymentIncludesQuery;

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
