<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Organization;

class OrganizationEndpoint extends RestEndpoint
{
    protected string $resourcePath = "organizations";

    /**
     * @inheritDoc
     */
    protected function getResourceObject(): Organization
    {
        return new Organization($this->client);
    }

    /**
     * Retrieve an organization from Mollie.
     *
     * Will throw a ApiException if the organization id is invalid or the resource cannot be found.
     *
     * @param string $organizationId
     * @param array $parameters
     *
     * @return Organization
     * @throws ApiException
     */
    public function get(string $organizationId, array $parameters = []): Organization
    {
        if (empty($organizationId)) {
            throw new ApiException("Organization ID is empty.");
        }

        return parent::rest_read($organizationId, $parameters);
    }

    /**
     * Retrieve the current organization from Mollie.
     *
     * @param array $parameters
     *
     * @return Organization
     * @throws ApiException
     */
    public function current(array $parameters = []): Organization
    {
        return parent::rest_read('me', $parameters);
    }
}
