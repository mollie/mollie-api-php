<?php

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Helpers\Arr;
use Mollie\Api\Types\ClientQuery;

class GetPaginatedClientQuery extends Data
{
    private PaginatedQuery $paginatedQuery;

    private bool $embedOrganization;

    private bool $embedOnboarding;

    public function __construct(
        PaginatedQuery $paginatedQuery,
        bool $embedOrganization = false,
        bool $embedOnboarding = false
    ) {
        $this->paginatedQuery = $paginatedQuery;
        $this->embedOrganization = $embedOrganization;
        $this->embedOnboarding = $embedOnboarding;
    }

    public function toArray(): array
    {
        return array_merge(
            $this->paginatedQuery->toArray(),
            [
                'embed' => Arr::join([
                    $this->embedOrganization ? ClientQuery::EMBED_ORGANIZATION : null,
                    $this->embedOnboarding ? ClientQuery::EMBED_ONBOARDING : null,
                ]),
            ]
        );
    }
}
