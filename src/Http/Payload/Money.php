<?php

namespace Mollie\Api\Http\Payload;

use Mollie\Api\Contracts\DataProvider;

class Money implements DataProvider
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

    public function data(): array
    {
        return [
            'currency' => $this->currency,
            'value' => $this->value,
        ];
    }
}
