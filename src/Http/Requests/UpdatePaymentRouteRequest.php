<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Payload\UpdatePaymentRoutePayload;
use Mollie\Api\Resources\Route;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class UpdatePaymentRouteRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInPayload
{
    use HasJsonPayload;

    /**
     * The HTTP method.
     */
    protected static string $method = Method::PATCH;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = Route::class;

    private string $paymentId;

    private string $routeId;

    private UpdatePaymentRoutePayload $payload;

    public function __construct(string $paymentId, string $routeId, UpdatePaymentRoutePayload $payload)
    {
        $this->paymentId = $paymentId;
        $this->routeId = $routeId;
        $this->payload = $payload;
    }

    protected function defaultPayload(): array
    {
        return $this->payload->toArray();
    }

    public function resolveResourcePath(): string
    {
        return "payments/{$this->paymentId}/routes/{$this->routeId}";
    }
}
