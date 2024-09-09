<?php

namespace Mollie\Api\Http\Query;

class GetPaginatedPaymentRefundQuery extends PaginatedQuery
{
    public bool $includePayment = false;

    public function __construct(
        bool $includePayment = false,
        ?string $from = null,
        ?int $limit = null,
        ?bool $testmode = null
    ) {
        parent::__construct($from, $limit, $testmode);

        $this->includePayment = $includePayment;
    }

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                'include' => $this->includePayment ? 'payment' : null,
            ]
        );
    }
}
