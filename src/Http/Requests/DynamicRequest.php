<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\AnyResource;

abstract class DynamicRequest extends ResourceHydratableRequest
{
    protected ?string $hydratableResource = AnyResource::class;

    public function __construct(
        private string $url,
    )
    {
    }

    public function resolveResourcePath(): string
    {
        return $this->url;
    }
}
