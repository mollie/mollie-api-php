<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetPaginatedSettlementRefundsQuery;

class GetPaginatedSettlementRefundsQueryFactory extends Factory
{
    public function create(): GetPaginatedSettlementRefundsQuery
    {
        $includePayment = $this->has(['filters.include', 'filters.includePayment']);

        return new GetPaginatedSettlementRefundsQuery(
            PaginatedQueryFactory::new($this->data)->create(),
            $this->get('includePayment', $includePayment)
        );
    }
}
