<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\GetPaymentMethodRequest;
use Mollie\Api\Types\MethodQuery;

class GetPaymentMethodRequestFactory extends RequestFactory
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function create(): GetPaymentMethodRequest
    {
        $includeIssuers = $this->queryIncludes('include', MethodQuery::INCLUDE_ISSUERS);
        $includePricing = $this->queryIncludes('include', MethodQuery::INCLUDE_PRICING);

        return new GetPaymentMethodRequest(
            $this->id,
            $this->query('locale'),
            $this->query('currency'),
            $this->query('profileId'),
            $this->query('includeIssuers', $includeIssuers),
            $this->query('includePricing', $includePricing),
        );
    }
}
