<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Repositories\ArrayStore;

trait HasHeaders
{
    protected ?ArrayStore $headers;

    public function headers(): ArrayStore
    {
        return $this->headers ??= new ArrayStore($this->defaultHeaders());
    }

    protected function defaultHeaders(): array
    {
        return [];
    }
}
