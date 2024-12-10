<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Types\PaymentIncludesQuery;
use Mollie\Api\Contracts\Arrayable;

class GetPaginatedRefundsQuery implements Arrayable
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
