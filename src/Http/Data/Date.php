<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data;

readonly class Date extends Temporal
{
    public const FORMAT = 'Y-m-d';

    protected function getFormat(): string
    {
        return self::FORMAT;
    }
}
