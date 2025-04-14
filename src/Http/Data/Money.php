<?php

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Arrayable;

class Money implements Arrayable
{
    public string $currency;

    public string $value;

    public function __construct(
        string $currency,
        string $value
    ) {
        $this->currency = $currency;
        $this->value = $value;
    }

    public function toArray(): array
    {
        return [
            'currency' => $this->currency,
            'value' => $this->value,
        ];
    }
}
