<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetPaymentRefundQuery;
use Mollie\Api\Types\PaymentIncludesQuery;
class GetPaymentRefundQueryFactory extends Factory
{
    public function create(): GetPaymentRefundQuery
    {
        $includePayment = $this->includes('include', PaymentIncludesQuery::PAYMENT);

        return new GetPaymentRefundQuery(
            $this->get('includePayment', $includePayment),
        );
    }
}
