<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetPaymentQuery;

class GetPaymentQueryFactory extends Factory
{
    public function create(): GetPaymentQuery
    {
        return new GetPaymentQuery(
            $this->get('embed', []),
            $this->get('include', []),
            $this->get('testmode')
        );
    }
}
