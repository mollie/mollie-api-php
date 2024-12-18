<?php

namespace Mollie\Api\Http\Requests;

abstract class DynamicRequest extends ResourceHydratableRequest
{
    private string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function resolveResourcePath(): string
    {
        return $this->url;
    }
}
