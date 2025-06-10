<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\GetEnabledMethodsRequest;
use Mollie\Api\Types\MethodQuery;
use Mollie\Api\Types\SequenceType;

class GetEnabledMethodsRequestFactory extends RequestFactory
{
    public function create(): GetEnabledMethodsRequest
    {
        $includeIssuers = $this->queryIncludes('include', MethodQuery::INCLUDE_ISSUERS);

        return new GetEnabledMethodsRequest(
            $this->query('sequenceType', SequenceType::ONEOFF),
            $this->query('resource', MethodQuery::RESOURCE_PAYMENTS),
            $this->query('locale'),
            $this->transformFromQuery('amount', fn ($item) => MoneyFactory::new($item)->create()),
            $this->query('billingCountry'),
            $this->query('includeWallets'),
            $this->query('orderLineCategories', []),
            $this->query('profileId'),
            $this->query('includeIssuers', $includeIssuers),
        );
    }
}
