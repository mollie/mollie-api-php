<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\GetPaginatedChargebackQuery;

class GetPaginatedChargebackQueryFactory extends Factory
{
    public function create(): GetPaginatedChargebackQuery
    {
        return new GetPaginatedChargebackQuery(
            PaginatedQueryFactory::new($this->data)->create(),
            $this->get('includePayment', false),
            $this->get('profileId')
        );
    }
}
