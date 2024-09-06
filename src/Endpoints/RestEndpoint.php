<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Contracts\SingleResourceEndpointContract;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\Requests\DynamicDeleteRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\DynamicPatchRequest;
use Mollie\Api\Http\Requests\DynamicPostRequest;
use Mollie\Api\Resources\BaseResource;
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
        return $this
            ->client
            ->send(new DynamicPostRequest(
                $this->getResourcePath(),
                static::getResourceClass(),
                $body,
                $filters
            ))
            ->toResource();
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

        return $this
            ->client
            ->send(new DynamicPatchRequest(
                $this->getPathToSingleResource($id),
                static::getResourceClass(),
                $body
            ))
            ->toResource();
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

        return $this
            ->client
            ->send(new DynamicGetRequest(
                $this->getPathToSingleResource($id),
                static::getResourceClass(),
                $filters
            ))
            ->toResource();
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

        return $this
            ->client
            ->send(new DynamicDeleteRequest(
                $this->getPathToSingleResource($id),
                static::getResourceClass(),
                null,
                $body
            ))
            ->toResource();
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
