<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Helpers\Arr;
use Mollie\Api\Types\PaymentIncludesQuery;
use Mollie\Api\Contracts\Arrayable;

class GetPaymentCaptureQuery implements Arrayable
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
