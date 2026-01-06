<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\WebhookEvent;
use Mollie\Api\Types\Method;

class GetWebhookEventRequest extends ResourceHydratableRequest
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = WebhookEvent::class;

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
        return "events/{$this->id}";
    }
}
