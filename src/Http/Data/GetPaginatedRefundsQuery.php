<?php

namespace Mollie\Api\Http\Data;

use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaginatedRefundsQuery extends Data
{
    private PaginatedQuery $paginatedQuery;

    private bool $embedPayment;

    private ?string $profileId;

    public function __construct(
        PaginatedQuery $paginatedQuery,
        bool $embedPayment = false,
        ?string $profileId = null
    ) {
        $this->paginatedQuery = $paginatedQuery;
        $this->embedPayment = $embedPayment;
        $this->profileId = $profileId;
    }

    public function toArray(): array
    {
        return array_merge(
            $this->paginatedQuery->toArray(),
            [
                'embed' => $this->embedPayment ? PaymentIncludesQuery::PAYMENT : null,
                'profileId' => $this->profileId,
            ]
        );
    }
}
