<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetAllMethodsQuery;

class GetAllMethodsQueryFactory extends Factory
{
    public function create(): GetAllMethodsQuery
    {
        return new GetAllMethodsQuery(
            $this->get('locale'),
            $this->get('include', []),
            $this->mapIfNotNull('amount', MoneyFactory::class)
        );
    }
}
