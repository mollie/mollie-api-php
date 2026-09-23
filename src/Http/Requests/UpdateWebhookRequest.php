<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Resources\Webhook;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;
use Mollie\Api\Utils\Arr;
use Mollie\Api\Utils\Utility;

/**
 * @see https://docs.mollie.com/reference/v2/webhooks-api/update-webhook
 *
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\Webhook>
 */
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
    protected ?string $hydratableResource = Webhook::class;

    public function __construct(
        private string $id,
        private $url,
        private $name,
        private $eventTypes,
    ) {
    }

    protected function defaultPayload(): array
    {
        return [
            'url' => $this->url,
            'name' => $this->name,
            'eventTypes' => Utility::transform(
                $this->eventTypes,
                fn ($eventTypes) => is_string($eventTypes) ? $eventTypes : Arr::join($eventTypes),
            ),
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
