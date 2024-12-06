<?php

namespace Mollie\Api\Http\Query;

class PaginatedQuery extends Query
{
    public ?string $from = null;

    public ?int $limit = null;

    public function __construct(
        ?string $from = null,
        ?int $limit = null
    ) {
        $this->from = $from;
        $this->limit = $limit;
    }

    public function toArray(): array
    {
        return [
            'from' => $this->from,
            'limit' => $this->limit,
        ];
    }
}
