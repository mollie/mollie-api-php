<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetPaginatedClientQuery;
use Mollie\Api\Types\ClientQuery;
class GetPaginatedClientQueryFactory extends Factory
{
    public function create(): GetPaginatedClientQuery
    {
        $embedOrganization = $this->includes('embed', ClientQuery::EMBED_ORGANIZATION);
        $embedOnboarding = $this->includes('embed', ClientQuery::EMBED_ONBOARDING);

        return new GetPaginatedClientQuery(
            PaginatedQueryFactory::new($this->data)->create(),
            $this->get('embedOrganization', $embedOrganization),
            $this->get('embedOnboarding', $embedOnboarding),
        );
    }
}
