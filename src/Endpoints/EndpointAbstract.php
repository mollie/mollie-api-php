<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Contracts\SingleResourceEndpoint;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\BaseResource;
use Mollie\Api\Resources\ResourceFactory;

abstract class EndpointAbstract
{
    public const REST_CREATE = MollieApiClient::HTTP_POST;
    public const REST_UPDATE = MollieApiClient::HTTP_PATCH;
    public const REST_READ = MollieApiClient::HTTP_GET;
    public const REST_LIST = MollieApiClient::HTTP_GET;
    public const REST_DELETE = MollieApiClient::HTTP_DELETE;

    protected MollieApiClient $client;

    protected string $resourcePath;

    protected ?string $parentId;

    public function __construct(MollieApiClient $api)
    {
        $this->client = $api;
    }

    /**
     * @param array $filters
     * @return string
     */
    protected function buildQueryString(array $filters): string
    {
        if (empty($filters)) {
            return "";
        }

        foreach ($filters as $key => $value) {
            if ($value === true) {
                $filters[$key] = "true";
            }

            if ($value === false) {
                $filters[$key] = "false";
            }
        }

        return "?" . http_build_query($filters, "", "&");
    }

    /**
     * @param array $body
     * @param array $filters
     * @return BaseResource
     * @throws ApiException
     */
    protected function rest_create(array $body, array $filters): BaseResource
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
    protected function rest_update(string $id, array $body = []): ?BaseResource
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
    protected function rest_read(string $id, array $filters): BaseResource
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
     * Sends a DELETE request to a single Molle API object.
     *
     * @param string $id
     * @param array $body
     *
     * @return null|BaseResource
     * @throws ApiException
     */
    protected function rest_delete(string $id, array $body = []): ?BaseResource
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

    /**
     * @param string $resourcePath
     */
    public function setResourcePath($resourcePath)
    {
        $this->resourcePath = strtolower($resourcePath);
    }

    /**
     * @return string
     * @throws ApiException
     */
    public function getResourcePath(): string
    {
        if (strpos($this->resourcePath, "_") !== false) {
            [$parentResource, $childResource] = explode("_", $this->resourcePath, 2);

            if (empty($this->parentId)) {
                throw new ApiException("Subresource '{$this->resourcePath}' used without parent '$parentResource' ID.");
            }

            return "$parentResource/{$this->parentId}/$childResource";
        }

        return $this->resourcePath;
    }

    protected function getPathToSingleResource(string $id): string
    {
        if ($this instanceof SingleResourceEndpoint) {
            return $this->getResourcePath();
        }

        return "{$this->getResourcePath()}/{$id}";
    }

    /**
     * @param array $body
     * @return null|string
     */
    protected function parseRequestBody(array $body): ?string
    {
        if (empty($body)) {
            return null;
        }

        return @json_encode($body);
    }
}
