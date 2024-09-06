<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Contracts\ArrayRepository;
use Mollie\Api\Repositories\ArrayStore;

trait HasHeaders
{
    protected ArrayRepository $headers;

    public function headers(): ArrayRepository
    {
        return $this->headers ??= new ArrayStore($this->defaultHeaders());
    }

    protected function defaultHeaders(): array
    {
        return [];
    }
}
