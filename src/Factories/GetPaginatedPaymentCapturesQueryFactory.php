<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\GetPaginatedPaymentCapturesQuery;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaginatedPaymentCapturesQueryFactory extends OldFactory
{
    public function create(): GetPaginatedPaymentCapturesQuery
    {
        $includePayments = $this->includes('include', PaymentIncludesQuery::PAYMENT);

        return new GetPaginatedPaymentCapturesQuery(
            PaginatedQueryFactory::new($this->data)->create(),
            $this->get('includePayments', $includePayments)
        );
    }
}
