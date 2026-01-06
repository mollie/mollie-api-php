<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\GetClientRequest;
use Mollie\Api\Types\ClientQuery;

class GetClientRequestFactory extends RequestFactory
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function create(): GetClientRequest
    {
        // Legacy: historically this factory accepted `embedOrganization` directly; Mollie uses `embed=organization`.
        $embedOrganization = $this->queryIncludes('embed', ClientQuery::EMBED_ORGANIZATION);

        // Legacy: historically this factory accepted `embedOnboarding` directly; Mollie uses `embed=onboarding`.
        $embedOnboarding = $this->queryIncludes('embed', ClientQuery::EMBED_ONBOARDING);

        return new GetClientRequest(
            $this->id,
            $this->query('embedOrganization', $embedOrganization),
            $this->query('embedOnboarding', $embedOnboarding),
        );
    }
}
