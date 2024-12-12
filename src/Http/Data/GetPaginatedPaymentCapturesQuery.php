<?php

namespace Mollie\Api\Http\Data;

use Mollie\Api\Utils\Arr;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaginatedPaymentCapturesQuery extends Data
{
    private PaginatedQuery $paginatedQuery;

    public bool $includePayment = false;

    public function __construct(
        PaginatedQuery $paginatedQuery,
        bool $includePayment = false
    ) {
        $this->paginatedQuery = $paginatedQuery;
        $this->includePayment = $includePayment;
    }

    public function toArray(): array
    {
        return array_merge(
            $this->paginatedQuery->toArray(),
            [
                'include' => Arr::join($this->includePayment ? [PaymentIncludesQuery::PAYMENT] : []),
            ]
        );
    }
}
