<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Http\Requests\GetOnboardingRequest as GetOnboardingStatusRequest;
use Mollie\Api\Resources\Onboarding;

class OnboardingEndpointCollection extends EndpointCollection
{
    public function status(): Onboarding
    {
        return $this->send(new GetOnboardingStatusRequest);
    }
}