<?php

namespace Mollie\Api\Http\Data;

class SortablePaginatedQuery extends PaginatedQuery
{
    public ?string $sort = null;

    public function __construct(
        ?string $from = null,
        ?int $limit = null,
        ?string $sort = null
    ) {
        parent::__construct($from, $limit);

        $this->sort = $sort;
    }

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                'sort' => $this->sort,
            ]
        );
    }
}
