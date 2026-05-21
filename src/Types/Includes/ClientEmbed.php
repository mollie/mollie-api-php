<?php

declare(strict_types=1);

namespace Mollie\Api\Types\Includes;

use Mollie\Api\Types\ClientQuery;

enum ClientEmbed: string
{
    case Organization = ClientQuery::EMBED_ORGANIZATION;
    case Onboarding = ClientQuery::EMBED_ONBOARDING;
}
