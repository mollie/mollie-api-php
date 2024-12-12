<?php

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Utils\Arr;

abstract class Data implements Arrayable
{
    public function resolve(): array
    {
        return Arr::resolve($this);
    }
}
