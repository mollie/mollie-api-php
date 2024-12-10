<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Types\PaymentIncludesQuery;
use Mollie\Api\Contracts\Arrayable;

class GetPaginatedPaymentRefundQuery implements Arrayable
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
                'include' => $this->includePayment ? PaymentIncludesQuery::PAYMENT : null,
            ]
        );
    }
}
