<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\Route;
use Mollie\Api\Types\Method;

/**
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\Route>
 */
class GetPaymentRouteRequest extends ResourceHydratableRequest
{
    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = Route::class;

    public function __construct(
        private string $paymentId,
        private string $routeId,
    ) {
    }

    public function resolveResourcePath(): string
    {
        return "payments/{$this->paymentId}/routes/{$this->routeId}";
    }
}
