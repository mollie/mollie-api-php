<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\GetAllMethodsRequest;
use Mollie\Api\Types\MethodQuery;

class GetAllPaymentMethodsRequestFactory extends RequestFactory
{
    public function create(): GetAllMethodsRequest
    {
        // Legacy: historically this factory accepted `includeIssuers` directly; Mollie uses `include=issuers`.
        $includeIssuers = $this->queryHas('includeIssuers')
            ? (bool) $this->query('includeIssuers')
            : $this->queryIncludes('include', MethodQuery::INCLUDE_ISSUERS);

        // Legacy: historically this factory accepted `includePricing` directly; Mollie uses `include=pricing`.
        $includePricing = $this->queryHas('includePricing')
            ? (bool) $this->query('includePricing')
            : $this->queryIncludes('include', MethodQuery::INCLUDE_PRICING);

        return new GetAllMethodsRequest(
            $includeIssuers,
            $includePricing,
            $this->query('locale'),
            $this->transformFromQuery('amount', fn ($item) => MoneyFactory::new($item)->create())
        );
    }
}
