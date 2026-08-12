<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data;

readonly class SortablePaginatedQuery extends PaginatedQuery
{
    public function __construct(
        ?string $from = null,
        ?int $limit = null,
        public ?string $sort = null,
    ) {
        parent::__construct($from, $limit);
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
