<?php

namespace Mollie\Api\Http\Data;

use Mollie\Api\Types\ClientQuery;
use Mollie\Api\Utils\Arr;

class GetClientQuery extends Data
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
