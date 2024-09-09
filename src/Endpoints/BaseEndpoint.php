<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Contracts\SingleResourceEndpointContract;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Types\Method;

abstract class BaseEndpoint
{
    public const REST_CREATE = Method::POST;

    public const REST_UPDATE = Method::PATCH;

    public const REST_READ = Method::GET;

    public const REST_LIST = Method::GET;

    public const REST_DELETE = Method::DELETE;

    protected MollieApiClient $client;

    /**
     * The resource path.
     */
    protected string $resourcePath;

    protected ?string $parentId;

    public function __construct(MollieApiClient $api)
    {
        $this->client = $api;
    }

    protected function buildQueryString(array $filters): string
    {
        if (empty($filters)) {
            return '';
        }

        foreach ($filters as $key => $value) {
            if ($value === true) {
                $filters[$key] = 'true';
            }

            if ($value === false) {
                $filters[$key] = 'false';
            }
        }

        return '?'.http_build_query($filters, '', '&');
    }

    /**
     * @throws ApiException
     */
    public function getResourcePath(): string
    {
        if (strpos($this->resourcePath, '_') !== false) {
            [$parentResource, $childResource] = explode('_', $this->resourcePath, 2);

            $this->guardAgainstMissingParentId($parentResource);

            return "$parentResource/{$this->parentId}/$childResource";
        }

        return $this->resourcePath;
    }

    protected function getPathToSingleResource(string $id): string
    {
        if ($this instanceof SingleResourceEndpointContract) {
            return $this->getResourcePath();
        }

        return "{$this->getResourcePath()}/{$id}";
    }

    protected function parseRequestBody(array $body): ?string
    {
        if (empty($body)) {
            return null;
        }

        return @json_encode($body);
    }

    private function guardAgainstMissingParentId(string $parentResource): void
    {
        if (empty($this->parentId)) {
            throw new ApiException("Subresource '{$this->resourcePath}' used without parent '$parentResource' ID.");
        }
    }
}
