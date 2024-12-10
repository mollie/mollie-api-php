<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Contracts\Arrayable;

class GetPaginatedBalanceQuery implements Arrayable
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
