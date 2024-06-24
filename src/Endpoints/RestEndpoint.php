<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Contracts\SingleResourceEndpoint;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\BaseResource;
use Mollie\Api\Resources\ResourceFactory;

abstract class RestEndpoint extends BaseEndpoint
{
    /**
     * @param array $body
     * @param array $filters
     * @return BaseResource
     * @throws ApiException
     */
    protected function createResource(array $body, array $filters): BaseResource
    {
        $result = $this->client->performHttpCall(
            self::REST_CREATE,
            $this->getResourcePath() . $this->buildQueryString($filters),
            $this->parseRequestBody($body)
        );

        return ResourceFactory::createFromApiResult($result, $this->getResourceObject());
    }

    /**
     * Sends a PATCH request to a single Mollie API object.
     *
     * @param string $id
     * @param array $body
     *
     * @return null|BaseResource
     * @throws ApiException
     */
    protected function updateResource(string $id, array $body = []): ?BaseResource
    {
        if (empty($id)) {
            throw new ApiException("Invalid resource id.");
        }

        $id = urlencode($id);
        $result = $this->client->performHttpCall(
            self::REST_UPDATE,
            $this->getPathToSingleResource($id),
            $this->parseRequestBody($body)
        );

        if ($result == null) {
            return null;
        }

        return ResourceFactory::createFromApiResult($result, $this->getResourceObject());
    }

    /**
     * Retrieves a single object from the REST API.
     *
     * @param string $id Id of the object to retrieve.
     * @param array $filters
     * @return BaseResource
     * @throws ApiException
     */
    protected function readResource(string $id, array $filters): BaseResource
    {
        if (!$this instanceof SingleResourceEndpoint && empty($id)) {
            throw new ApiException("Invalid resource id.");
        }

        $id = urlencode($id);
        $result = $this->client->performHttpCall(
            self::REST_READ,
            $this->getPathToSingleResource($id) . $this->buildQueryString($filters)
        );

        return ResourceFactory::createFromApiResult($result, $this->getResourceObject());
    }

    /**
     * Sends a DELETE request to a single Mollie API object.
     *
     * @param string $id
     * @param array $body
     *
     * @return null|BaseResource
     * @throws ApiException
     */
    protected function deleteResource(string $id, array $body = []): ?BaseResource
    {
        if (empty($id)) {
            throw new ApiException("Invalid resource id.");
        }

        $id = urlencode($id);
        $result = $this->client->performHttpCall(
            self::REST_DELETE,
            $this->getPathToSingleResource($id),
            $this->parseRequestBody($body)
        );

        if ($result == null) {
            return null;
        }

        return ResourceFactory::createFromApiResult($result, $this->getResourceObject());
    }

    /**
     * Get the object that is used by this API endpoint. Every API endpoint uses one type of object.
     *
     * @return BaseResource
     */
    abstract protected function getResourceObject(): BaseResource;
}
