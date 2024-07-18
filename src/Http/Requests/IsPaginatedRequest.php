<?php

namespace Mollie\Api\Http\Requests;

trait IsPaginatedRequest
{
    public ?string $from = null;

    public ?int $limit = null;

    public array $filters = [];

    public function __construct(
        ?string $from = null,
        ?int $limit = null,
        array $filters = []
    ) {
        $this->from = $from;
        $this->limit = $limit;
        $this->filters = $filters;
    }

    public function getQuery(): array
    {
        return array_merge([
            'from' => $this->from,
            'limit' => $this->limit,
        ], $this->filters);
    }
}
