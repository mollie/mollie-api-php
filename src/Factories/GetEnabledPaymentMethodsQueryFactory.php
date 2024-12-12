<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\GetEnabledPaymentMethodsQuery;
use Mollie\Api\Types\MethodQuery;

class GetEnabledPaymentMethodsQueryFactory extends Factory
{
    public function create(): GetEnabledPaymentMethodsQuery
    {
        $includeIssuers = $this->includes('include', MethodQuery::INCLUDE_ISSUERS);
        $includePricing = $this->includes('include', MethodQuery::INCLUDE_PRICING);

        return new GetEnabledPaymentMethodsQuery(
            $this->get('sequenceType', MethodQuery::SEQUENCE_TYPE_ONEOFF),
            $this->get('resource', MethodQuery::RESOURCE_PAYMENTS),
            $this->get('locale'),
            $this->mapIfNotNull('amount', MoneyFactory::class),
            $this->get('billingCountry'),
            $this->get('includeWallets'),
            $this->get('orderLineCategories', []),
            $this->get('profileId'),
            $this->get('includeIssuers', $includeIssuers),
            $this->get('includePricing', $includePricing),
        );
    }
}
