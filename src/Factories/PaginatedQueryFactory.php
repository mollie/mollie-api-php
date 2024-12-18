<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\PaginatedQuery;

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
