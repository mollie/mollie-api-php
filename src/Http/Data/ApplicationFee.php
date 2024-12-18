<?php

namespace Mollie\Api\Http\Data;

class ApplicationFee extends Data
{
    public Money $amount;

    public string $description;

    public function __construct(
        Money $amount,
        string $description
    ) {
        $this->amount = $amount;
        $this->description = $description;
    }

    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'description' => $this->description,
        ];
    }
}
