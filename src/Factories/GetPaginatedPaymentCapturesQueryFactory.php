<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetPaginatedPaymentCapturesQuery;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaginatedPaymentCapturesQueryFactory extends Factory
{
    public function create(): GetPaginatedPaymentCapturesQuery
    {
        $includePayments = $this->includes('include', PaymentIncludesQuery::PAYMENT);

        return new GetPaginatedPaymentCapturesQuery(
            PaginatedQueryFactory::new($this->data)->create(),
            $this->get('includePayments', $includePayments)
        );
    }
}
