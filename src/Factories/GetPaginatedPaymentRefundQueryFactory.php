<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetPaginatedPaymentRefundQuery;

class GetPaginatedPaymentRefundQueryFactory extends Factory
{
    public function create(): GetPaginatedPaymentRefundQuery
    {
        return new GetPaginatedPaymentRefundQuery(
            $this->has('includePayment') || $this->get('filters.include') === 'payment',
            $this->get('from'),
            $this->get('limit'),
            $this->get('testmode', $this->get('filters.testmode'))
        );
    }
}
