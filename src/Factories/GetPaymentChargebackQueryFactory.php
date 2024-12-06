<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetPaymentChargebackQuery;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaymentChargebackQueryFactory extends Factory
{
    public function create(): GetPaymentChargebackQuery
    {
        $includePayment = $this->includes('include', PaymentIncludesQuery::PAYMENT);

        return new GetPaymentChargebackQuery(
            $this->get('includePayment', $includePayment),
        );
    }
}
