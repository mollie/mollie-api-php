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
    public static function getResourceClass(): string
    {
        return  Organization::class;
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

        /** @var Organization */
        return $this->readResource($organizationId, $parameters);
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
        /** @var Organization */
        return $this->readResource('me', $parameters);
    }
}
