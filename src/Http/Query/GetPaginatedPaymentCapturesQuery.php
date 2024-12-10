<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Helpers\Arr;
use Mollie\Api\Types\PaymentIncludesQuery;
use Mollie\Api\Contracts\Arrayable;

class GetPaginatedPaymentCapturesQuery implements Arrayable
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
