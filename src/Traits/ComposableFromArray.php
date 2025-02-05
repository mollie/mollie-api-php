<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Http\Data\DataCollection;

trait ComposableFromArray
{
    public static function fromArray($data = []): self
    {
        $data = DataCollection::wrap($data)->toArray();

        /** @phpstan-ignore-next-line */
        return new static(...$data);
    }
}
