<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Traits\ComposableFromArray;
use Mollie\Api\Types\PaymentQuery;

class CreatePaymentQuery implements Arrayable
{
    use ComposableFromArray;

    private bool $includeQrCode;

    public function __construct(
        bool $includeQrCode = false
    ) {
        $this->includeQrCode = $includeQrCode;
    }

    public function toArray(): array
    {
        return [
            'include' => $this->includeQrCode ? PaymentQuery::INCLUDE_QR_CODE : null,
        ];
    }
}
