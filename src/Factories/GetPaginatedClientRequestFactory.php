<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\GetPaginatedClientRequest;
use Mollie\Api\Types\ClientQuery;

class GetPaginatedClientRequestFactory extends RequestFactory
{
    public function create(): GetPaginatedClientRequest
    {
        $embedOrganization = $this->queryIncludes('embed', ClientQuery::EMBED_ORGANIZATION);
        $embedOnboarding = $this->queryIncludes('embed', ClientQuery::EMBED_ONBOARDING);

        return new GetPaginatedClientRequest(
            $this->query('from'),
            $this->query('limit'),
            $this->query('embedOrganization', $embedOrganization),
            $this->query('embedOnboarding', $embedOnboarding),
        );
    }
}
