<?php

namespace Mollie\Api\Webhooks;

use Mollie\Api\Resources\BaseResource;

class WebhookEvent
{
    private string $type;

    private BaseResource $resource;

    public function __construct(string $type, BaseResource $resource)
    {
        $this->type = $type;
        $this->resource = $resource;
    }

    public function is(string $type): bool
    {
        return $this->type === $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getResource(): BaseResource
    {
        return $this->resource;
    }
}
