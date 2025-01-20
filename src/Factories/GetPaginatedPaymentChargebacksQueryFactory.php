<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\GetPaginatedPaymentChargebacksQuery;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaginatedPaymentChargebacksQueryFactory extends OldFactory
{
    public function create(): GetPaginatedPaymentChargebacksQuery
    {
        $includePayment = $this->includes('include', PaymentIncludesQuery::PAYMENT);

        return new GetPaginatedPaymentChargebacksQuery(
            PaginatedQueryFactory::new($this->data)->create(),
            $this->get('includePayment', $includePayment),
        );
    }
}
