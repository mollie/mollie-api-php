<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Resolvable;

readonly class OrderLine implements Resolvable
{
    public function __construct(
        public string $description,
        public int $quantity,
        public Money $unitPrice,
        public Money $totalAmount,
        public ?string $type = null,
        public ?string $quantityUnit = null,
        public ?Money $discountAmount = null,
        public ?RecurringBillingCycle $recurring = null,
        public ?string $vatRate = null,
        public ?Money $vatAmount = null,
        public ?string $sku = null,
        public ?string $imageUrl = null,
        public ?string $productUrl = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'description' => $this->description,
            'quantity' => $this->quantity,
            'unitPrice' => $this->unitPrice,
            'totalAmount' => $this->totalAmount,
            'type' => $this->type,
            'quantityUnit' => $this->quantityUnit,
            'discountAmount' => $this->discountAmount,
            'recurring' => $this->recurring,
            'vatRate' => $this->vatRate,
            'vatAmount' => $this->vatAmount,
            'sku' => $this->sku,
            'imageUrl' => $this->imageUrl,
            'productUrl' => $this->productUrl,
        ];
    }
}
