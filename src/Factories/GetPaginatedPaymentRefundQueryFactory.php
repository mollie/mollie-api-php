<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\GetPaginatedPaymentRefundQuery;

class GetPaginatedPaymentRefundQueryFactory extends Factory
{
    public function create(): GetPaginatedPaymentRefundQuery
    {
        return new GetPaginatedPaymentRefundQuery(
            PaginatedQueryFactory::new($this->data)->create(),
            $this->get('includePayment', false)
        );
    }
}
