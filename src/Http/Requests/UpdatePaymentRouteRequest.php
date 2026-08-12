<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use DateTimeInterface;
use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Data\Date;
use Mollie\Api\Resources\Route;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

/**
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\Route>
 */
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
    protected ?string $hydratableResource = Route::class;

    /**
     * @param  DateTimeInterface|Date  $releaseDate
     */
    public function __construct(
        private string $paymentId,
        private string $routeId,
        private $releaseDate,
    ) {
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
