<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\Requests\EnableProfileMethodRequest;
use Mollie\Api\Http\Requests\DisableProfileMethodRequest;
use Mollie\Api\Resources\Method;
use Mollie\Api\Resources\Profile;

class ProfileMethodEndpointCollection extends EndpointCollection
{
    /**
     * Enable a method for the provided Profile ID.
     * Alias of enableForId for backwards compatibility.
     *
     * @param string $profileId
     * @param string $id
     * @throws ApiException
     */
    public function createForId(string $profileId, string $id): Method
    {
        return $this->enableForId($profileId, $id);
    }

    /**
     * Enable a method for the provided Profile object.
     * Alias of enableFor for backwards compatibility.
     *
     * @param Profile $profile
     * @param string $id
     * @throws ApiException
     */
    public function createFor(Profile $profile, string $id): Method
    {
        return $this->enableFor($profile, $id);
    }

    /**
     * Enable a method for the current profile.
     * Alias of enable for backwards compatibility.
     *
     * @param string $id
     * @throws ApiException
     */
    public function createForCurrentProfile(string $id): Method
    {
        return $this->enable($id);
    }

    /**
     * Enable a payment method for a specific profile.
     *
     * @param string $profileId The profile's ID or 'me' for the current profile
     * @param string $id The payment method ID
     * @throws ApiException
     */
    public function enableForId(string $profileId, string $id): Method
    {
        /** @var Method */
        return $this->send(new EnableProfileMethodRequest($profileId, $id));
    }

    /**
     * Enable a payment method for the provided Profile object.
     *
     * @param Profile $profile
     * @param string $id The payment method ID
     * @throws ApiException
     */
    public function enableFor(Profile $profile, string $id): Method
    {
        return $this->enableForId($profile->id, $id);
    }

    /**
     * Enable a payment method for the current profile.
     *
     * @param string $id The payment method ID
     * @throws ApiException
     */
    public function enable(string $id): Method
    {
        return $this->enableForId('me', $id);
    }

    /**
     * Disable a method for the provided Profile ID.
     * Alias of disableForId for backwards compatibility.
     *
     * @param string $profileId
     * @param string $id
     * @throws ApiException
     */
    public function deleteForId(string $profileId, string $id): void
    {
        $this->disableForId($profileId, $id);
    }

    /**
     * Disable a method for the provided Profile object.
     * Alias of disableFor for backwards compatibility.
     *
     * @param Profile $profile
     * @param string $id
     * @throws ApiException
     */
    public function deleteFor(Profile $profile, string $id): void
    {
        $this->disableFor($profile, $id);
    }

    /**
     * Disable a method for the current profile.
     * Alias of disable for backwards compatibility.
     *
     * @param string $id
     * @throws ApiException
     */
    public function deleteForCurrentProfile(string $id): void
    {
        $this->disable($id);
    }

    /**
     * Disable a payment method for a specific profile.
     *
     * @param string $profileId The profile's ID or 'me' for the current profile
     * @param string $id The payment method ID
     * @throws ApiException
     */
    public function disableForId(string $profileId, string $id): void
    {
        $this->send(new DisableProfileMethodRequest($profileId, $id));
    }

    /**
     * Disable a payment method for the provided Profile object.
     *
     * @param Profile $profile
     * @param string $id The payment method ID
     * @throws ApiException
     */
    public function disableFor(Profile $profile, string $id): void
    {
        $this->disableForId($profile->id, $id);
    }

    /**
     * Disable a payment method for the current profile.
     *
     * @param string $id The payment method ID
     * @throws ApiException
     */
    public function disable(string $id): void
    {
        $this->disableForId('me', $id);
    }
}
