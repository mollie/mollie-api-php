<?php

namespace Mollie\Api\Http\Requests;

abstract class DynamicRequest extends ResourceHydratableRequest
{
    private string $url;

    private string $resourceClass;

    public function __construct(string $url, string $resourceClass = '')
    {
        if (! empty($resourceClass) && ! class_exists($resourceClass)) {
            throw new \InvalidArgumentException("The resource class '{$resourceClass}' does not exist.");
        }

        $this->url = $url;
        $this->resourceClass = $resourceClass;
    }

    public function getTargetResourceClass(): string
    {
        return $this->resourceClass;
    }

    public function resolveResourcePath(): string
    {
        return $this->url;
    }
}
