<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Traits\ComposableFromArray;

/**
 * Why a payment is in its current status. Only present for some payment
 * methods (for example point-of-sale). `code` stays a raw string so codes
 * Mollie adds later are preserved unchanged.
 */
readonly class PaymentStatusReason implements Arrayable
{
    use ComposableFromArray;

    public function __construct(
        public string $code,
        public string $message,
    ) {
    }

    /** @return array{code: string, message: string} */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'message' => $this->message,
        ];
    }
}
