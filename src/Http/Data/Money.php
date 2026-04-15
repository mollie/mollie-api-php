<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Http\Data\Concerns\Macroable;
use Mollie\Api\Traits\ComposableFromArray;
use Mollie\Api\Traits\HasCurrencyConvenienceMethods;

readonly class Money implements Arrayable
{
    use ComposableFromArray;
    use HasCurrencyConvenienceMethods;
    use Macroable;

    public function __construct(
        public string $currency,
        public string $value,
    ) {}

    public function toArray(): array
    {
        return [
            'currency' => $this->currency,
            'value' => $this->value,
        ];
    }
}
