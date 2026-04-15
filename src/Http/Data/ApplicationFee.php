<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Resolvable;

readonly class ApplicationFee implements Resolvable
{
    public function __construct(
        public Money $amount,
        public string $description,
    ) {}

    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'description' => $this->description,
        ];
    }
}
