<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetPaymentCaptureQuery;

class GetPaymentCaptureQueryFactory extends Factory
{
    public function create(): GetPaymentCaptureQuery
    {
        return new GetPaymentCaptureQuery(
            $this->get('include', []),
        );
    }
}
