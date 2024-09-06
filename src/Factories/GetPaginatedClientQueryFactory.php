<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetPaginatedClientQuery;

class GetPaginatedClientQueryFactory extends Factory
{
    public function create(): GetPaginatedClientQuery
    {
        return new GetPaginatedClientQuery(
            $this->get('embed', $this->get('filters.embed', [])),
            $this->get('from'),
            $this->get('limit'),
        );
    }
}
