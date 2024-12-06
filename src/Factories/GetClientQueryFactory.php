<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetClientQuery;
use Mollie\Api\Types\ClientQuery;

class GetClientQueryFactory extends Factory
{
    public function create(): GetClientQuery
    {
        $embedOrganization = $this->includes('embed', ClientQuery::EMBED_ORGANIZATION);
        $embedOnboarding = $this->includes('embed', ClientQuery::EMBED_ONBOARDING);

        return new GetClientQuery(
            $this->get('embedOrganization', $embedOrganization),
            $this->get('embedOnboarding', $embedOnboarding),
        );
    }
}
