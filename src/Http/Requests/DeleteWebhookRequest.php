<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class DeleteWebhookRequest extends ResourceHydratableRequest implements SupportsTestmodeInPayload
{
    use HasJsonPayload;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::DELETE;

    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * The resource path.
     */
    public function resolveResourcePath(): string
    {
        return "webhooks/{$this->id}";
    }
}
