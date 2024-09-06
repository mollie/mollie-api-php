<?php

namespace Mollie\Api\Traits;

trait ComposableFromArray
{
    public static function fromArray(array $data): static
    {
        return new static(...$data);
    }
}
