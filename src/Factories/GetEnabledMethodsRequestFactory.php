<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\GetEnabledMethodsRequest;
use Mollie\Api\Types\MethodQuery;
use Mollie\Api\Types\SequenceType;

class GetEnabledMethodsRequestFactory extends RequestFactory
{
    public function create(): GetEnabledMethodsRequest
    {
        // Legacy: historically this factory accepted `includeIssuers` directly; Mollie uses `include=issuers`.
        $includeIssuers = $this->queryHas('includeIssuers')
            ? (bool) $this->query('includeIssuers')
            : $this->queryIncludes('include', MethodQuery::INCLUDE_ISSUERS);

        // Legacy: historically this factory accepted `includePricing` directly; Mollie uses `include=pricing`.
        $includePricing = $this->queryHas('includePricing')
            ? (bool) $this->query('includePricing')
            : $this->queryIncludes('include', MethodQuery::INCLUDE_PRICING);

        return new GetEnabledMethodsRequest(
            $this->query('sequenceType', SequenceType::ONEOFF),
            $this->query('resource', MethodQuery::RESOURCE_PAYMENTS),
            $this->query('locale'),
            $this->transformFromQuery('amount', fn ($item) => MoneyFactory::new($item)->create()),
            $this->query('billingCountry'),
            $this->query('includeWallets'),
            $this->query('orderLineCategories', []),
            $this->query('profileId'),
            $includeIssuers,
            $includePricing,
        );
    }
}
