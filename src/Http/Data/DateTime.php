<?php

namespace Mollie\Api\Http\Data;

use DateTimeInterface;

class DateTime extends Temporal
{
    protected function getFormat(): string
    {
        return DateTimeInterface::ATOM;
    }
}
