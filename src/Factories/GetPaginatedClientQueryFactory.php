<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetPaginatedClientQuery;

class GetPaginatedClientQueryFactory extends Factory
{
    private PaginatedQueryFactory $paginatedQueryFactory;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->paginatedQueryFactory = new PaginatedQueryFactory($attributes);
    }

    public function create(): GetPaginatedClientQuery
    {
        return new GetPaginatedClientQuery(
            $this->paginatedQueryFactory->create(),
            $this->get('embed', [])
        );
    }
}
