<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetPaginatedInvoiceQuery;

class GetPaginatedInvoiceQueryFactory extends Factory
{
    public function create(): GetPaginatedInvoiceQuery
    {
        $reference = $this->get('filters.reference');
        $year = $this->get('filters.year');

        return new GetPaginatedInvoiceQuery(
            PaginatedQueryFactory::new($this->data)->create(),
            $this->get('reference', $reference),
            $this->get('year', $year)
        );
    }
}
