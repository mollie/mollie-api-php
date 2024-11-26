<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetPaginatedSettlementCapturesQuery;

class GetPaginatedSettlementCapturesQueryFactory extends Factory
{
    public function create(): GetPaginatedSettlementCapturesQuery
    {
        return new GetPaginatedSettlementCapturesQuery(
            PaginatedQueryFactory::new($this->data)->create(),
            $this->get('include', [])
        );
    }
}
