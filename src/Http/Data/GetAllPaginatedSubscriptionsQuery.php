<?php

namespace Mollie\Api\Http\Data;

class GetAllPaginatedSubscriptionsQuery extends Data
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
