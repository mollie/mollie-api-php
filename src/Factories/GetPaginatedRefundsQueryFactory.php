<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetPaginatedRefundsQuery;

class GetPaginatedRefundsQueryFactory extends Factory
{
    public function create(): GetPaginatedRefundsQuery
    {
        return new GetPaginatedRefundsQuery(
            PaginatedQueryFactory::new($this->data)->create(),
            $this->get('embed'),
            $this->get('profileId')
        );
    }
}
