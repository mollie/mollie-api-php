<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Contracts\Arrayable;

class GetPaginatedSettlementsQuery implements Arrayable
{
    private ?string $balanceId = null;

    private PaginatedQuery $query;

    public function __construct(
        PaginatedQuery $query,
        ?string $balanceId = null
    ) {
        $this->query = $query;
        $this->balanceId = $balanceId;
    }

    public function toArray(): array
    {
        return array_merge(
            $this->query->toArray(),
            $this->balanceId ? ['balanceId' => $this->balanceId] : []
        );
    }
}
