<?php

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Traits\ComposableFromArray;

class Discount implements Arrayable
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

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'value' => $this->value,
        ];
    }
}
