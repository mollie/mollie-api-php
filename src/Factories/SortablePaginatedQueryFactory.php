<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\SortablePaginatedQuery;

class SortablePaginatedQueryFactory extends Factory
{
    public function create(): SortablePaginatedQuery
    {
        $sort = $this->get('filters.sort');
        $testmode = $this->get('filters.testmode');

        return new SortablePaginatedQuery(
            $this->get('from'),
            $this->get('limit'),
            $this->get('sort', $sort),
            $this->get('testmode', $testmode)
        );
    }
}
