<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Exceptions\ApiException;

class CurrentProfile extends Profile
{
    /**
     * Enable a payment method for this profile.
     *
     *
     * @throws ApiException
     */
    public function enableMethod(string $methodId): Method
    {
        return $this->connector->profileMethods->createForCurrentProfile($methodId);
    }

    /**
     * Disable a payment method for this profile.
     *
     *
     * @throws ApiException
     */
    public function disableMethod(string $methodId): void
    {
        $this->connector->profileMethods->deleteForCurrentProfile($methodId);
    }
}
