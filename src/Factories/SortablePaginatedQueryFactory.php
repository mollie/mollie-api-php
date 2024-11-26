<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\SortablePaginatedQuery;

class SortablePaginatedQueryFactory extends Factory
{
    public function create(): SortablePaginatedQuery
    {
        return new SortablePaginatedQuery(
            $this->get('from'),
            $this->get('limit'),
            $this->get('sort'),
        );
    }
}
