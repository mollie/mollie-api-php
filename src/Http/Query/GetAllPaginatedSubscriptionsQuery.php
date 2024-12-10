<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Contracts\Arrayable;

class GetAllPaginatedSubscriptionsQuery implements Arrayable
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
