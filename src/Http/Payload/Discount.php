<?php

namespace Mollie\Api\Http\Payload;

use Mollie\Api\Contracts\DataProvider;
use Mollie\Api\Traits\ComposableFromArray;

class Discount implements DataProvider
{
    use ComposableFromArray;

    public string $type;

    public string $value;

    public function __construct(
        string $type,
        string $value
    ) {
        $this->type = $type;
        $this->value = $value;
    }

    public function data()
    {
        return [
            'type' => $this->type,
            'value' => $this->value,
        ];
    }
}
