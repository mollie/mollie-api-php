<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetPaginatedPaymentChargebacksQuery;

class GetPaginatedPaymentChargebacksQueryFactory extends Factory
{
    public function create(): GetPaginatedPaymentChargebacksQuery
    {
        $include = $this->get('filters.include', []);

        return new GetPaginatedPaymentChargebacksQuery(
            PaginatedQueryFactory::new($this->data)->create(),
            $this->get('include', $include),
        );
    }
}
