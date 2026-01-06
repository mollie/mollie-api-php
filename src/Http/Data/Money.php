<?php

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Traits\ComposableFromArray;

class Money implements Arrayable
{
    use ComposableFromArray;

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
