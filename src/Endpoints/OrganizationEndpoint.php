<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Method;
use Mollie\Api\Resources\Organization;

class OrganizationEndpoint extends EndpointAbstract
{
    protected $resourcePath = "organizations";

    /**
     * @return Organization
     */
    protected function getResourceObject()
    {
        return new Organization($this->api);
    }

    /**
     * Get the collection object that is used by this API endpoint. Every API endpoint uses one type of collection object.
     *
     * @param int $count
     * @param object[] $_links
     *
     * @return OrganizationCollection
     */
    protected function getResourceCollectionObject($count, $_links)
    {
        return new OrganizationCollection($count, $_links);
    }

    /**
     * Retrieve an organization from Mollie.
     *
     * Will throw a ApiException if the organization id is invalid or the resource cannot be found.
     *
     * @param string $organizationId
     * @param array $parameters
     * @return Method
     * @throws ApiException
     */
    public function get($organizationId, array $parameters = [])
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
     * @return Method
     * @throws ApiException
     */
    public function current(array $parameters = [])
    {
        return parent::rest_read('me', $parameters);
    }

    /**
     * Retrieve all organizations.
     *
     * @param array $parameters
     *
     * @return OrganizationCollection
     * @throws ApiException
     */
    public function all(array $parameters = [])
    {
        return parent::rest_list(null, null, $parameters);
    }
}
