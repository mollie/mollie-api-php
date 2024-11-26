<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetPaymentChargebackQuery;

class GetPaymentChargebackQueryFactory extends Factory
{
    public function create(): GetPaymentChargebackQuery
    {
        return new GetPaymentChargebackQuery(
            include: $this->get('include', []),
            testmode: $this->get('testmode', null)
        );
    }
}
