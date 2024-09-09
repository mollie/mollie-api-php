<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\PaginatedQuery;

class PaginatedQueryFactory extends Factory
{
    public function create(): PaginatedQuery
    {
        $testmode = $this->get('filters.testmode');

        return new PaginatedQuery(
            $this->get('from'),
            $this->get('limit'),
            $this->get('testmode', $testmode)
        );
    }
}
