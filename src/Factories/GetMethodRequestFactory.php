<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\GetMethodRequest;
use Mollie\Api\Types\MethodQuery;

class GetMethodRequestFactory extends RequestFactory
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function create(): GetMethodRequest
    {
        // Legacy: historically this factory accepted `includeIssuers` directly; Mollie uses `include=issuers`.
        $includeIssuers = $this->queryHas('includeIssuers')
            ? (bool) $this->query('includeIssuers')
            : $this->queryIncludes('include', MethodQuery::INCLUDE_ISSUERS);

        // Legacy: historically this factory accepted `includePricing` directly; Mollie uses `include=pricing`.
        $includePricing = $this->queryHas('includePricing')
            ? (bool) $this->query('includePricing')
            : $this->queryIncludes('include', MethodQuery::INCLUDE_PRICING);

        return new GetMethodRequest(
            $this->id,
            $this->query('locale'),
            $this->query('currency'),
            $this->query('profileId'),
            $includeIssuers,
            $includePricing,
        );
    }
}
