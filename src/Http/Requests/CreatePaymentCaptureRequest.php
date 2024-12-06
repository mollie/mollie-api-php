<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Payload\CreatePaymentCapturePayload;
use Mollie\Api\Resources\Capture;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class CreatePaymentCaptureRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInPayload
{
    use HasJsonPayload;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::POST;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = Capture::class;

    private string $paymentId;

    private CreatePaymentCapturePayload $payload;

    public function __construct(string $paymentId, CreatePaymentCapturePayload $payload)
    {
        $this->paymentId = $paymentId;
        $this->payload = $payload;
    }

    protected function defaultPayload(): array
    {
        return $this->payload->toArray();
    }

    public function resolveResourcePath(): string
    {
        return "payments/{$this->paymentId}/captures";
    }
}
