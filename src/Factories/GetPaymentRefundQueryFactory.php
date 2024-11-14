<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetPaymentRefundQuery;

class GetPaymentRefundQueryFactory extends Factory
{
    public function create(): GetPaymentRefundQuery
    {
        return new GetPaymentRefundQuery(
            $this->get('include', []),
            $this->get('testmode')
        );
    }
}
