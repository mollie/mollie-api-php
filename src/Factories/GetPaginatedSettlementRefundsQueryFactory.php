<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\GetPaginatedSettlementRefundsQuery;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaginatedSettlementRefundsQueryFactory extends Factory
{
    public function create(): GetPaginatedSettlementRefundsQuery
    {
        $includePayment = $this->includes('include', PaymentIncludesQuery::PAYMENT);

        return new GetPaginatedSettlementRefundsQuery(
            PaginatedQueryFactory::new($this->data)->create(),
            $this->get('includePayment', $includePayment)
        );
    }
}
