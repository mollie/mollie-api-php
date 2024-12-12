<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Contracts\ArrayRepository;
use Mollie\Api\Repositories\ArrayStore;

trait HasQuery
{
    protected ArrayRepository $queryStore;

    public function query(): ArrayRepository
    {
        return $this->queryStore ??= new ArrayStore($this->defaultQuery());
    }

    protected function defaultQuery(): array
    {
        return [];
    }
}
