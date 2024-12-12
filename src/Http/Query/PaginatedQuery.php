<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Contracts\Arrayable;

class PaginatedQuery implements Arrayable
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