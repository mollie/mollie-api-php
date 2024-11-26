<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Http\Payload\UpdatePaymentLinkPayload;
use Mollie\Api\Resources\PaymentLink;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;

class UpdatePaymentLinkRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInQuery
{
    use HasJsonPayload;

    protected static string $method = Method::PATCH;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = PaymentLink::class;

    private string $id;

    private UpdatePaymentLinkPayload $payload;

    public function __construct(string $id, UpdatePaymentLinkPayload $payload)
    {
        $this->id = $id;
        $this->payload = $payload;
    }

    protected function defaultPayload(): array
    {
        return $this->payload->toArray();
    }

    public function resolveResourcePath(): string
    {
        return "payment-links/{$this->id}";
    }
}
