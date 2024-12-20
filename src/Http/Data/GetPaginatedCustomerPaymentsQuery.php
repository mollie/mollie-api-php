<?php

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Resolvable;

class GetPaginatedCustomerPaymentsQuery implements Resolvable
{
    private SortablePaginatedQuery $paginatedQuery;

    public ?string $profileId = null;

    public function __construct(
        SortablePaginatedQuery $paginatedQuery,
        ?string $profileId = null
    ) {
        $this->paginatedQuery = $paginatedQuery;
        $this->profileId = $profileId;
    }

    public function toArray(): array
    {
        return array_merge(
            $this->paginatedQuery->toArray(),
            [
                'profileId' => $this->profileId,
            ]
        );
    }
}
