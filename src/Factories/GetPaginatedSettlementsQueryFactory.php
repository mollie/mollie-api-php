<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\GetPaginatedSettlementsQuery;

class GetPaginatedSettlementsQueryFactory extends Factory
{
    /**
     * Create a new instance of GetPaginatedSettlementsQuery.
     */
    public function create(): GetPaginatedSettlementsQuery
    {
        $balanceId = $this->get('balanceId');

        return new GetPaginatedSettlementsQuery(
            PaginatedQueryFactory::new($this->data)->create(),
            $this->get('filters.balanceId', $balanceId),
        );
    }
}
