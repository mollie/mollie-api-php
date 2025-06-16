<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Resources\Webhook;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;
use Mollie\Api\Utils\Arr;

class UpdateWebhookRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInPayload
{
    use HasJsonPayload;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::PATCH;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = Webhook::class;

    private string $id;

    private string $url;

    private string $name;

    /**
     * @var string|array
     */
    private $eventTypes;

    public function __construct(
        string $id,
        string $url,
        string $name,
        $eventTypes
    ) {
        $this->id = $id;
        $this->url = $url;
        $this->name = $name;
        $this->eventTypes = $eventTypes;
    }

    protected function defaultPayload(): array
    {
        return [
            'url' => $this->url,
            'name' => $this->name,
            'eventTypes' => is_string($this->eventTypes) ? $this->eventTypes : Arr::join($this->eventTypes),
        ];
    }

    /**
     * The resource path.
     */
    public function resolveResourcePath(): string
    {
        return "webhooks/{$this->id}";
    }
}
