<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Contracts\SingleResourceEndpointContract;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Partner;

class OrganizationPartnerEndpoint extends RestEndpoint implements SingleResourceEndpointContract
{
    protected string $resourcePath = "organizations/me/partner";

    /**
     * @inheritDoc
     */
    protected function getResourceObject(): Partner
    {
        return new Partner($this->client);
    }

    /**
     * Retrieve details about the partner status of the currently authenticated organization.
     *
     * Will throw an ApiException if the resource cannot be found.
     *
     * @return Partner
     * @throws ApiException
     */
    public function get(): Partner
    {
        return $this->readResource('', []);
    }
}
