<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaginatedPaymentChargebacksQuery extends Query
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
                'include' => $this->includePayment ? PaymentIncludesQuery::PAYMENT : [],
            ]
        );
    }
}