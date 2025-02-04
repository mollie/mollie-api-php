<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\PaginatedQuery;

class PaginatedQueryFactory extends RequestFactory
{
    public function create(): PaginatedQuery
    {
        return new PaginatedQuery(
            $this->query('from'),
            $this->query('limit'),
        );
    }
}
