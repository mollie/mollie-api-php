<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\GetPaymentChargebackQuery;
use Mollie\Api\Types\PaymentIncludesQuery;

class GetPaymentChargebackQueryFactory extends OldFactory
{
    public function create(): GetPaymentChargebackQuery
    {
        $includePayment = $this->includes('include', PaymentIncludesQuery::PAYMENT);

        return new GetPaymentChargebackQuery(
            $this->get('includePayment', $includePayment),
        );
    }
}
