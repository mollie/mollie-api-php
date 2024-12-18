<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\GetAllPaginatedSubscriptionsQuery;

class GetAllPaginatedSubscriptionsQueryFactory extends Factory
{
    public function create(): GetAllPaginatedSubscriptionsQuery
    {
        return new GetAllPaginatedSubscriptionsQuery(
            PaginatedQueryFactory::new([
                'from' => $this->get('from'),
                'limit' => $this->get('limit'),
            ])->create(),
            $this->get('profileId')
        );
    }
}
