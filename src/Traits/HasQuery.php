<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Repositories\ArrayStore;

trait HasQuery
{
    protected ?ArrayStore $queryStore;

    public function query(): ArrayStore
    {
        return $this->queryStore ??= new ArrayStore($this->defaultQuery());
    }

    protected function defaultQuery(): array
    {
        return [];
    }
}
