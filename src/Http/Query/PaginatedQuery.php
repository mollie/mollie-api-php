<?php

namespace Mollie\Api\Http\Query;

class PaginatedQuery extends Query
{
    public ?string $from = null;

    public ?int $limit = null;

    public ?bool $testmode = null;

    public function __construct(
        ?string $from = null,
        ?int $limit = null,
        ?bool $testmode = null
    ) {
        $this->from = $from;
        $this->limit = $limit;
        $this->testmode = $testmode;
    }

    public function toArray(): array
    {
        return [
            'from' => $this->from,
            'limit' => $this->limit,
            'testmode' => $this->testmode,
        ];
    }
}
