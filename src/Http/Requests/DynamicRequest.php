<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\AnyResource;

abstract class DynamicRequest extends ResourceHydratableRequest
{
    private string $url;

    protected $hydratableResource = AnyResource::class;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function resolveResourcePath(): string
    {
        return $this->url;
    }
}
