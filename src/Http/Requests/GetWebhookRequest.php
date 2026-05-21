<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\Webhook;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/get-webhook
 *
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\Webhook>
 */
class GetWebhookRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = Webhook::class;

    public function __construct(
        private string $id,
    ) {
    }

    /**
     * The resource path.
     */
    public function resolveResourcePath(): string
    {
        return "webhooks/{$this->id}";
    }
}
