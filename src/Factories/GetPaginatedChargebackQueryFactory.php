<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetPaginatedChargebackQuery;

class GetPaginatedChargebackQueryFactory extends Factory
{
    public function create(): GetPaginatedChargebackQuery
    {
        return new GetPaginatedChargebackQuery(
            $this->has('includePayment') || $this->get('filters.include') === 'payment',
            $this->get('profileId', $this->get('filters.profileId')),
            $this->get('from'),
            $this->get('limit'),
            $this->get('testmode', $this->get('filters.testmode'))
        );
    }
}
