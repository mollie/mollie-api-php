<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Contracts\SingleResourceEndpointContract;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\BaseResource;
use Mollie\Api\Resources\ResourceFactory;
use Mollie\Api\Traits\InteractsWithResource as TraitsInteractsWithResource;
use RuntimeException;

abstract class RestEndpoint extends BaseEndpoint
{
    use TraitsInteractsWithResource;

    /**
     * Resource id prefix.
     * Used to validate resource id's.
     */
    protected static string $resourceIdPrefix;

    /**
     * @throws ApiException
     */
    protected function createResource(array $body, array $filters): BaseResource
    {
        $result = $this->client->performHttpCall(
            self::REST_CREATE,
            $this->getResourcePath().$this->buildQueryString($filters),
            $this->parseRequestBody($body)
        );

        return ResourceFactory::createFromApiResult($this->client, $result->decode(), static::getResourceClass());
    }

    /**
     * Sends a PATCH request to a single Mollie API object.
     *
     *
     * @throws ApiException
     */
    protected function updateResource(string $id, array $body = []): ?BaseResource
    {
        $id = urlencode($id);

        $response = $this->client->performHttpCall(
            self::REST_UPDATE,
            $this->getPathToSingleResource($id),
            $this->parseRequestBody($body)
        );

        if ($response->isEmpty()) {
            return null;
        }

        return ResourceFactory::createFromApiResult($this->client, $response->decode(), static::getResourceClass());
    }

    /**
     * Retrieves a single object from the REST API.
     *
     * @param  string  $id  Id of the object to retrieve.
     *
     * @throws ApiException
     */
    protected function readResource(string $id, array $filters): BaseResource
    {
        if (! $this instanceof SingleResourceEndpointContract && empty($id)) {
            throw new ApiException('Invalid resource id.');
        }

        $id = urlencode($id);
        $response = $this->client->performHttpCall(
            self::REST_READ,
            $this->getPathToSingleResource($id).$this->buildQueryString($filters)
        );

        return ResourceFactory::createFromApiResult($this->client, $response->decode(), static::getResourceClass());
    }

    /**
     * Sends a DELETE request to a single Mollie API object.
     *
     *
     * @throws ApiException
     */
    protected function deleteResource(string $id, array $body = []): ?BaseResource
    {
        if (empty($id)) {
            throw new ApiException('Invalid resource id.');
        }

        $id = urlencode($id);
        $response = $this->client->performHttpCall(
            self::REST_DELETE,
            $this->getPathToSingleResource($id),
            $this->parseRequestBody($body)
        );

        if ($response->isEmpty()) {
            return null;
        }

        return ResourceFactory::createFromApiResult($this->client, $response->decode(), static::getResourceClass());
    }

    protected function guardAgainstInvalidId(string $id): void
    {
        if (empty(static::$resourceIdPrefix)) {
            throw new RuntimeException('Resource ID prefix is not set.');
        }

        if (empty($id) || strpos($id, static::$resourceIdPrefix) !== 0) {
            $resourceType = $this->getResourceType();

            throw new ApiException("Invalid {$resourceType} ID: '{$id}'. A resource ID should start with '".static::$resourceIdPrefix."'.");
        }
    }

    public function getResourceType(): string
    {
        $classBasename = basename(str_replace('\\', '/', static::getResourceClass()));

        return strtolower($classBasename);
    }
}
