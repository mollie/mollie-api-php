<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Resources\Subscription;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/v2/subscriptions-api/get-subscription
 */
class GetSubscriptionRequest extends ResourceHydratableRequest implements SupportsTestmodeInQuery
{
    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = Subscription::class;

    public function __construct(
        private string $customerId,
        private string $id,
    )
    {
    }

    public function resolveResourcePath(): string
    {
        return "customers/{$this->customerId}/subscriptions/{$this->id}";
    }
}
