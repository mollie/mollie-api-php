<?php

namespace Mollie\Api\Http\Payload;

class ApplicationFee extends DataBag
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

    public function data(): array
    {
        return [
            'amount' => $this->amount,
            'description' => $this->description,
        ];
    }
}
