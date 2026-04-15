<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Resources\Webhook;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;
use Mollie\Api\Utils\Arr;

/**
 * @see https://docs.mollie.com/reference/create-webhook
 */
class CreateWebhookRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInPayload
{
    use HasJsonPayload;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::POST;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = Webhook::class;

    public function __construct(
        private string $url,
        private string $name,
        private $eventTypes,
    ) {
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
        return 'webhooks';
    }
}
