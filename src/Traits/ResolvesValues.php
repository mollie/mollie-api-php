<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Contracts\DataResolver;
use Mollie\Api\Http\Payload\DataCollection;
use Stringable;

trait ResolvesValues
{
    public function resolve(): array
    {
        return DataCollection::wrap($this)
            ->map(function ($value) {
                if ($value instanceof DataResolver) {
                    return $value->resolve();
                }

                if ($value instanceof Arrayable) {
                    return $value->toArray();
                }

                if ($value instanceof Stringable) {
                    return $value->__toString();
                }

                return $value;
            })
            ->filter()
            ->toArray();
    }
}
