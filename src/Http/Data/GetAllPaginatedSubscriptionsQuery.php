<?php

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Resolvable;

class GetAllPaginatedSubscriptionsQuery implements Resolvable
{
    private ?string $profileId;

    private PaginatedQuery $paginatedQuery;

    public function __construct(PaginatedQuery $paginatedQuery, ?string $profileId = null)
    {
        $this->paginatedQuery = $paginatedQuery;
        $this->profileId = $profileId;
    }

    public function toArray(): array
    {
        return array_merge($this->paginatedQuery->toArray(), [
            'profileId' => $this->profileId,
        ]);
    }
}
