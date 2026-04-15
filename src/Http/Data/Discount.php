<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Traits\ComposableFromArray;

readonly class Discount implements Arrayable
{
    use ComposableFromArray;

    public function __construct(
        public string $type,
        public string $value,
    ) {}

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'value' => $this->value,
        ];
    }
}
