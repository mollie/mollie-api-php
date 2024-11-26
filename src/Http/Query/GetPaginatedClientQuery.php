<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Helpers\Arr;

class GetPaginatedClientQuery extends Query
{
    private PaginatedQuery $paginatedQuery;

    public array $embed = [];

    public function __construct(
        PaginatedQuery $paginatedQuery,
        array $embed = []
    ) {
        $this->paginatedQuery = $paginatedQuery;
        $this->embed = $embed;
    }

    public function toArray(): array
    {
        return array_merge(
            $this->paginatedQuery->toArray(),
            [
                'embed' => Arr::join($this->embed),
            ]
        );
    }
}
