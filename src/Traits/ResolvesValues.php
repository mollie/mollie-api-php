<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Contracts\DataProvider;
use Mollie\Api\Contracts\DataResolver;
use Mollie\Api\Http\Payload\DataCollection;

trait ResolvesValues
{
    public function resolve(): array
    {
        return DataCollection::wrap($this)
            ->map(function ($value) {
                if ($value instanceof DataResolver) {
                    return $value->resolve();
                }

                if ($value instanceof DataProvider) {
                    return $value->data();
                }

                return $value;
            })
            ->filter()
            ->toArray();
    }
}
