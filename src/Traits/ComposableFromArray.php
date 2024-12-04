<?php

namespace Mollie\Api\Traits;

trait ComposableFromArray
{
    public static function fromArray(array $data = []): self
    {
        /** @phpstan-ignore-next-line */
        return new static(...$data);
    }
}
