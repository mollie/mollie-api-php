<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\SortablePaginatedQuery;

class SortablePaginatedQueryFactory extends RequestFactory
{
    public function create(): SortablePaginatedQuery
    {
        return new SortablePaginatedQuery(
            $this->query('from'),
            $this->query('limit'),
            $this->query('sort'),
        );
    }
}
