<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetPaginatedPaymentCapturesQuery;

class GetPaginatedPaymentCapturesQueryFactory extends Factory
{
    public function create(): GetPaginatedPaymentCapturesQuery
    {
        $include = $this->get('filters.include', []);

        return new GetPaginatedPaymentCapturesQuery(
            PaginatedQueryFactory::new($this->data)->create(),
            $this->get('include', $include)
        );
    }
}
