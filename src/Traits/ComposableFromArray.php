<?php

namespace Mollie\Api\Traits;

trait ComposableFromArray
{
    public static function fromArray(array $data): self
    {
        return new static(...$data);
    }
}
