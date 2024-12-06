<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Helpers\Arr;
use Mollie\Api\Types\ClientQuery;

class GetClientQuery extends Query
{
    private bool $embedOrganization;

    private bool $embedOnboarding;

    public function __construct(
        bool $embedOrganization = false,
        bool $embedOnboarding = false
    ) {
        $this->embedOrganization = $embedOrganization;
        $this->embedOnboarding = $embedOnboarding;
    }

    public function toArray(): array
    {
        return [
            'embed' => Arr::join([
                $this->embedOrganization ? ClientQuery::EMBED_ORGANIZATION : null,
                $this->embedOnboarding ? ClientQuery::EMBED_ONBOARDING : null,
            ]),
        ];
    }
}
