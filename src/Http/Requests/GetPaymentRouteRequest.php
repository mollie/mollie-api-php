<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\Route;
use Mollie\Api\Types\Method;

class GetPaymentRouteRequest extends ResourceHydratableRequest
{
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = Route::class;

    private string $paymentId;

    private string $routeId;

    public function __construct(string $paymentId, string $routeId)
    {
        $this->paymentId = $paymentId;
        $this->routeId = $routeId;
    }

    public function resolveResourcePath(): string
    {
        return "payments/{$this->paymentId}/routes/{$this->routeId}";
    }
}
