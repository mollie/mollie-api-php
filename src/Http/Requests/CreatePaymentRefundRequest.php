<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Data\CreateRefundPaymentPayload;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class CreatePaymentRefundRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInPayload
{
    use HasJsonPayload;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::POST;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = \Mollie\Api\Resources\Refund::class;

    private string $paymentId;

    private CreateRefundPaymentPayload $payload;

    public function __construct(
        string $identifier,
        CreateRefundPaymentPayload $payload
    ) {
        $this->paymentId = $identifier;
        $this->payload = $payload;
    }

    protected function defaultPayload(): array
    {
        return $this->payload->toArray();
    }

    public function resolveResourcePath(): string
    {
        return "payments/{$this->paymentId}/refunds";
    }
}
