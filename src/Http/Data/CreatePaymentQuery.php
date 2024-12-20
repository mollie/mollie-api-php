<?php

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Resolvable;
use Mollie\Api\Traits\ComposableFromArray;
use Mollie\Api\Types\PaymentQuery;

class CreatePaymentQuery implements Resolvable
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
