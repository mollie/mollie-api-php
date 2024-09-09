<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Exceptions\ApiException;

class CurrentProfile extends Profile
{
    /**
     * Enable a payment method for this profile.
     *
     * @param  string  $methodId
     *
     * @throws ApiException
     */
    public function enableMethod($methodId, array $data = []): Method
    {
        return $this->connector->profileMethods->createForCurrentProfile($methodId, $data);
    }

    /**
     * Disable a payment method for this profile.
     *
     * @param  string  $methodId
     *
     * @throws ApiException
     */
    public function disableMethod($methodId, array $data = []): ?Method
    {
        return $this->connector->profileMethods->deleteForCurrentProfile($methodId, $data);
    }
}
