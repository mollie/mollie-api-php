<?php

declare(strict_types=1);

namespace Mollie\Api\Endpoints;


use Mollie\Api\Resources\Issuer;

class MethodIssuerEndpoint extends EndpointAbstract
{
    protected $resourcePath = 'profiles_methods_issuers';

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