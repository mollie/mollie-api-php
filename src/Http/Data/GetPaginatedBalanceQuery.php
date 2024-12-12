<?php

namespace Mollie\Api\Http\Data;

class GetPaginatedBalanceQuery extends Data
{
    private SortablePaginatedQuery $paginatedQuery;

    public ?string $currency;

    public function __construct(
        SortablePaginatedQuery $paginatedQuery,
        ?string $currency = null
    ) {
        $this->paginatedQuery = $paginatedQuery;
        $this->currency = $currency;
    }

    public function toArray(): array
    {
        return array_merge(
            $this->paginatedQuery->toArray(),
            [
                'currency' => $this->currency,
            ]
        );
    }
}
