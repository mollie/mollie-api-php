<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\GetPaginatedBalanceQuery;

class GetPaginatedBalanceQueryFactory extends Factory
{
    public function create(): GetPaginatedBalanceQuery
    {
        return new GetPaginatedBalanceQuery(
            SortablePaginatedQueryFactory::new($this->data)->create(),
            $this->get('currency')
        );
    }
}
