<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetPaymentMethodQuery;

class GetPaymentMethodQueryFactory extends Factory
{
    public function create(): GetPaymentMethodQuery
    {
        return new GetPaymentMethodQuery(
            $this->get('locale'),
            $this->get('currency'),
            $this->get('profileId'),
            $this->get('include'),
        );
    }
}
