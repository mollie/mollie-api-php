<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetEnabledMethodsQuery;
use Mollie\Api\Types\MethodQuery;

class GetEnabledMethodsQueryFactory extends Factory
{
    public function create(): GetEnabledMethodsQuery
    {
        return new GetEnabledMethodsQuery(
            $this->get('sequenceType', MethodQuery::SEQUENCE_TYPE_ONEOFF),
            $this->get('resource', MethodQuery::RESOURCE_PAYMENTS),
            $this->get('locale'),
            $this->mapIfNotNull('amount', MoneyFactory::class),
            $this->get('billingCountry'),
            $this->get('includeWallets'),
            $this->get('orderLineCategories', []),
            $this->get('profileId'),
            $this->get('include', []),
            $this->get('testmode')
        );
    }
}
