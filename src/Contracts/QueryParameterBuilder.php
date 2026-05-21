<?php

declare(strict_types=1);

namespace Mollie\Api\Contracts;

interface QueryParameterBuilder extends Stringable
{
    public function toQueryValue(): string;
}
