<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Contracts\Arrayable;

class GetPaginatedCustomerPaymentsQuery implements Arrayable
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
