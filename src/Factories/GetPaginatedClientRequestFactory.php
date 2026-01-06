<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\GetPaginatedClientRequest;
use Mollie\Api\Types\ClientQuery;

class GetPaginatedClientRequestFactory extends RequestFactory
{
    public function create(): GetPaginatedClientRequest
    {
        // Legacy: historically this factory accepted `embedOrganization` directly; Mollie uses `embed=organization`.
        $embedOrganization = $this->queryIncludes('embed', ClientQuery::EMBED_ORGANIZATION);

        // Legacy: historically this factory accepted `embedOnboarding` directly; Mollie uses `embed=onboarding`.
        $embedOnboarding = $this->queryIncludes('embed', ClientQuery::EMBED_ONBOARDING);

        return new GetPaginatedClientRequest(
            $this->query('from'),
            $this->query('limit'),
            $this->query('embedOrganization', $embedOrganization),
            $this->query('embedOnboarding', $embedOnboarding),
        );
    }
}
