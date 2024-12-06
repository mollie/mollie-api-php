<?php

namespace Mollie\Api\Http\Query;

class GetPaginatedInvoiceQuery extends Query
{
    private PaginatedQuery $paginatedQuery;

    public ?string $reference;

    public ?string $year;

    public function __construct(
        PaginatedQuery $paginatedQuery,
        ?string $reference = null,
        ?string $year = null
    ) {
        $this->paginatedQuery = $paginatedQuery;
        $this->reference = $reference;
        $this->year = $year;
    }

    public function toArray(): array
    {
        return array_merge(
            $this->paginatedQuery->toArray(),
            [
                'reference' => $this->reference,
                'year' => $this->year,
            ]
        );
    }
}
