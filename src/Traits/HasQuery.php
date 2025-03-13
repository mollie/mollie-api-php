<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Contracts\Repository;
use Mollie\Api\Repositories\QueryStore;

trait HasQuery
{
    protected Repository $queryStore;

    public function query(): Repository
    {
        return $this->queryStore ??= new QueryStore($this->defaultQuery());
    }

    protected function defaultQuery(): array
    {
        return [];
    }
}
