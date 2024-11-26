<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\PaginatedQuery;

class PaginatedQueryFactory extends Factory
{
    public function create(): PaginatedQuery
    {
        return new PaginatedQuery(
            $this->get('from'),
            $this->get('limit'),
        );
    }
}
