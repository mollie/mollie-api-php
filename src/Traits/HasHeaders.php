<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Contracts\Repository;
use Mollie\Api\Repositories\ArrayStore;

trait HasHeaders
{
    protected Repository $headers;

    public function headers(): Repository
    {
        return $this->headers ??= new ArrayStore($this->defaultHeaders());
    }

    protected function defaultHeaders(): array
    {
        return [];
    }
}
