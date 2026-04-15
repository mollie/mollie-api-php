<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\Onboarding;
use Mollie\Api\Types\Method;

class GetOnboardingStatusRequest extends ResourceHydratableRequest
{
    protected static string $method = Method::GET;

    protected ?string $hydratableResource = Onboarding::class;

    public function resolveResourcePath(): string
    {
        return 'onboarding/me';
    }
}
