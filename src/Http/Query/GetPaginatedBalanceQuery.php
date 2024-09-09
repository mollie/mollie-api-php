<?php

namespace Mollie\Api\Http\Query;

class GetPaginatedBalanceQuery extends PaginatedQuery
{
    public ?string $currency;

    public function __construct(
        ?string $currency = null,
        ?string $from = null,
        ?int $limit = null,
        ?bool $testmode = null
    ) {
        parent::__construct($from, $limit, $testmode);

        $this->currency = $currency;
    }

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                'currency' => $this->currency,
            ]
        );
    }
}
