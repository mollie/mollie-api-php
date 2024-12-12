<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Http\Data\CreatePaymentLinkPayload;
use Mollie\Api\Resources\PaymentLink;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class CreatePaymentLinkRequest extends ResourceHydratableRequest implements HasPayload
{
    use HasJsonPayload;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::POST;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = PaymentLink::class;

    private CreatePaymentLinkPayload $payload;

    public function __construct(CreatePaymentLinkPayload $payload)
    {
        $this->payload = $payload;
    }

    protected function defaultPayload(): array
    {
        return $this->payload->toArray();
    }

    /**
     * The resource path.
     */
    public function resolveResourcePath(): string
    {
        return 'payment-links';
    }
}
