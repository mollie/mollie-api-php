<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetPaginatedBalanceQuery;

class GetPaginatedBalanceQueryFactory extends Factory
{
    public function create(): GetPaginatedBalanceQuery
    {
        $testmode = $this->get('filters.testmode');
        $currency = $this->get('filters.currency');

        return new GetPaginatedBalanceQuery(
            $this->get('currency', $currency),
            $this->get('from'),
            $this->get('limit'),
            $this->get('testmode', $testmode)
        );
    }
}
