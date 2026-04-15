<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Resolvable;

readonly class InvoiceLine implements Resolvable
{
    public function __construct(
        public string $description,
        public int $quantity,
        public string $vatRate,
        public Money $unitPrice,
        public ?Discount $discount = null,
    ) {}

    public function toArray(): array
    {
        return [
            'description' => $this->description,
            'quantity' => $this->quantity,
            'vatRate' => $this->vatRate,
            'unitPrice' => $this->unitPrice,
            'discount' => $this->discount,
        ];
    }
}
