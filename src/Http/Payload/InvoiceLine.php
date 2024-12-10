<?php

namespace Mollie\Api\Http\Payload;

class InvoiceLine extends DataBag
{
    public string $description;

    public int $quantity;

    public string $vatRate;

    public Money $unitPrice;

    public ?Discount $discount;

    public function __construct(
        string $description,
        int $quantity,
        string $vatRate,
        Money $unitPrice,
        ?Discount $discount = null
    ) {
        $this->description = $description;
        $this->quantity = $quantity;
        $this->vatRate = $vatRate;
        $this->unitPrice = $unitPrice;
        $this->discount = $discount;
    }

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
