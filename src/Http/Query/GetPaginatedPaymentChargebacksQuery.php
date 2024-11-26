<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Helpers\Arr;

class GetPaginatedPaymentChargebacksQuery extends Query
{
    private PaginatedQuery $paginatedQuery;

    public array $include = [];

    public function __construct(
        PaginatedQuery $paginatedQuery,
        array $include = []
    ) {
        $this->paginatedQuery = $paginatedQuery;
        $this->include = $include;
    }

    public function toArray(): array
    {
        return array_merge(
            $this->paginatedQuery->toArray(),
            [
                'include' => Arr::join($this->include),
            ]
        );
    }
}
