<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetPaginatedSettlementCapturesQuery;
use Mollie\Api\Types\PaymentIncludesQuery;
class GetPaginatedSettlementCapturesQueryFactory extends Factory
{
    public function create(): GetPaginatedSettlementCapturesQuery
    {
        $includePayment = $this->includes('include', PaymentIncludesQuery::PAYMENT);

        return new GetPaginatedSettlementCapturesQuery(
            PaginatedQueryFactory::new($this->data)->create(),
            $this->get('includePayment', $includePayment)
        );
    }
}
