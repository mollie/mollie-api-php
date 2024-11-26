<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Helpers\Arr;

class GetPaginatedRefundsQuery extends Query
{
    private PaginatedQuery $paginatedQuery;

    public array $embed = [];

    public ?string $profileId = null;

    public function __construct(
        PaginatedQuery $paginatedQuery,
        array $embed = [],
        ?string $profileId = null
    ) {
        $this->paginatedQuery = $paginatedQuery;
        $this->embed = $embed;
        $this->profileId = $profileId;
    }

    public function toArray(): array
    {
        return array_merge(
            $this->paginatedQuery->toArray(),
            [
                'embed' => Arr::join($this->embed),
                'profileId' => $this->profileId,
            ]
        );
    }
}
