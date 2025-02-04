<?php

namespace Mollie\Api\Http\Requests;

use DateTimeInterface;
use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
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
    protected $hydratableResource = Route::class;

    private string $paymentId;

    private string $routeId;

    private DateTimeInterface $releaseDate;

    public function __construct(string $paymentId, string $routeId, DateTimeInterface $releaseDate)
    {
        $this->paymentId = $paymentId;
        $this->routeId = $routeId;
        $this->releaseDate = $releaseDate;
    }

    protected function defaultPayload(): array
    {
        return [
            'releaseDate' => $this->releaseDate,
        ];
    }

    public function resolveResourcePath(): string
    {
        return "payments/{$this->paymentId}/routes/{$this->routeId}";
    }
}
