<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetPaginatedPaymentChargebacksQuery;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaginatedPaymentChargebacksQueryFactory extends Factory
{
    public function create(): GetPaginatedPaymentChargebacksQuery
    {
        $includePayment = $this->includes('include', PaymentIncludesQuery::PAYMENT);

        return new GetPaginatedPaymentChargebacksQuery(
            PaginatedQueryFactory::new($this->data)->create(),
            $this->get('includePayment', $includePayment),
        );
    }
}
