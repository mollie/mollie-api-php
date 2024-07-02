<?php

declare(strict_types=1);

namespace Mollie\Api\Endpoints;


use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Issuer;

class MethodIssuerEndpoint extends EndpointAbstract
{
    protected $resourcePath = 'profiles_methods_issuers';

    protected $profileId = null;
    protected $methodId = null;

    

    /**
     * @return string
     * @throws ApiException
     */
    public function getResourcePath()
    {
        if (! $this->profileId) {
            throw new ApiException("No profileId provided.");
        }

        if (! $this->methodId) {
            throw new ApiException("No methodId provided.");
        }

        return "profiles/{$this->profileId}/methods/{$this->methodId}/issuers";
    }

    /**
     * Get the object that is used by this API endpoint. Every API endpoint uses one type of object.
     *
     * @return Issuer
     */
    protected function getResourceObject()
    {
        return new Issuer($this->client);
    }
}