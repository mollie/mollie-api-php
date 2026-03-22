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
        $includeIssuers = $this->queryIncludes('include', MethodQuery::INCLUDE_ISSUERS);

        return new GetMethodRequest(
            $this->id,
            $this->query('locale'),
            $this->query('currency'),
            $this->query('profileId'),
            $this->query('includeIssuers', $includeIssuers),
        );
    }
}
