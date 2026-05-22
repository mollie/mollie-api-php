<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data;

readonly class MoneyBuilder
{
    public function __construct(
        private string $currency,
    ) {
    }

    public function minorUnits(int $amount): Money
    {
        return Money::fromMinorUnits($this->currency, $amount);
    }

    public function fromString(string $value): Money
    {
        return new Money($this->currency, $value);
    }
}
