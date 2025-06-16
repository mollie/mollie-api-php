<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\GetAllMethodsRequest;
use Mollie\Api\Types\MethodQuery;

class GetAllPaymentMethodsRequestFactory extends RequestFactory
{
    public function create(): GetAllMethodsRequest
    {
        $includeIssuers = $this->queryIncludes('include', MethodQuery::INCLUDE_ISSUERS);
        $includePricing = $this->queryIncludes('include', MethodQuery::INCLUDE_PRICING);

        return new GetAllMethodsRequest(
            $this->query('includeIssuers', $includeIssuers),
            $this->query('includePricing', $includePricing),
            $this->query('locale'),
            $this->transformFromQuery('amount', fn ($item) => MoneyFactory::new($item)->create()),
            $this->query('profileId')
        );
    }
}
