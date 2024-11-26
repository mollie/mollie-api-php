<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetPaginatedInvoiceQuery;

class GetPaginatedInvoiceQueryFactory extends Factory
{
    private PaginatedQueryFactory $paginatedQueryFactory;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->paginatedQueryFactory = new PaginatedQueryFactory($attributes);
    }

    public function create(): GetPaginatedInvoiceQuery
    {
        $reference = $this->get('filters.reference');
        $year = $this->get('filters.year');

        return new GetPaginatedInvoiceQuery(
            $this->paginatedQueryFactory->create(),
            $this->get('reference', $reference),
            $this->get('year', $year)
        );
    }
}
