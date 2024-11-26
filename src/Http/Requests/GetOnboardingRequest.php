<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\Onboarding;
use Mollie\Api\Types\Method;

class GetOnboardingRequest extends ResourceHydratableRequest
{
    protected static string $method = Method::GET;

    public static string $targetResourceClass = Onboarding::class;

    public function resolveResourcePath(): string
    {
        return 'onboarding/me';
    }
}
