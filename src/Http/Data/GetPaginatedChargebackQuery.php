<?php

namespace Mollie\Api\Http\Data;

use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaginatedChargebackQuery extends Data
{
    private PaginatedQuery $paginatedQuery;

    public bool $includePayment = false;

    public ?string $profileId = null;

    public function __construct(
        PaginatedQuery $paginatedQuery,
        bool $includePayment = false,
        ?string $profileId = null
    ) {
        $this->paginatedQuery = $paginatedQuery;
        $this->includePayment = $includePayment;
        $this->profileId = $profileId;
    }

    public function toArray(): array
    {
        return array_merge(
            $this->paginatedQuery->toArray(),
            [
                'include' => $this->includePayment ? PaymentIncludesQuery::PAYMENT : null,
                'profileId' => $this->profileId,
            ]
        );
    }
}