<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\GetAllMethodsQuery;
use Mollie\Api\Types\MethodQuery;

class GetAllPaymentMethodsQueryFactory extends OldFactory
{
    public function create(): GetAllMethodsQuery
    {
        $includeIssuers = $this->includes('include', MethodQuery::INCLUDE_ISSUERS);
        $includePricing = $this->includes('include', MethodQuery::INCLUDE_PRICING);

        return new GetAllMethodsQuery(
            $this->get('includeIssuers', $includeIssuers),
            $this->get('includePricing', $includePricing),
            $this->get('locale'),
            $this->mapIfNotNull('amount', MoneyFactory::class)
        );
    }
}
