<?php

namespace Mollie\Api\Http\Query;

class GetAllPaginatedSubscriptionsQuery extends Query
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
