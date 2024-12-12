<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\GetPaymentMethodQuery;
use Mollie\Api\Types\MethodQuery;

class GetPaymentMethodQueryFactory extends Factory
{
    public function create(): GetPaymentMethodQuery
    {
        $includeIssuers = $this->includes('include', MethodQuery::INCLUDE_ISSUERS);
        $includePricing = $this->includes('include', MethodQuery::INCLUDE_PRICING);

        return new GetPaymentMethodQuery(
            $this->get('locale'),
            $this->get('currency'),
            $this->get('profileId'),
            $this->get('includeIssuers', $includeIssuers),
            $this->get('includePricing', $includePricing),
        );
    }
}
