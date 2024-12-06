<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetPaginatedSettlementChargebacksQuery;

class GetPaginatedSettlementChargebacksQueryFactory extends Factory
{
    /**
     * Create a new instance of GetPaginatedSettlementChargebacksQuery.
     */
    public function create(): GetPaginatedSettlementChargebacksQuery
    {
        return new GetPaginatedSettlementChargebacksQuery(
            PaginatedQueryFactory::new($this->data)->create(),
            $this->get('includePayment', false),
            $this->get('profileId')
        );
    }
}
