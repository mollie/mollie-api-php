<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Resolvable;

readonly class PaginatedQuery implements Resolvable
{
    public function __construct(
        public ?string $from = null,
        public ?int $limit = null,
    ) {}

    public function toArray(): array
    {
        return [
            'from' => $this->from,
            'limit' => $this->limit,
        ];
    }
}
