<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Resources\Route;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

/**
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\Route>
 */
class CreateDelayedPaymentRouteRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInPayload
{
    use HasJsonPayload;

    /**
     * The HTTP method.
     */
    protected static string $method = Method::POST;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = Route::class;

    public function __construct(
        private string $paymentId,
        private Money $amount,
        private array $destination,
    ) {
    }

    protected function defaultPayload(): array
    {
        return [
            'amount' => $this->amount,
            'destination' => $this->destination,
        ];
    }

    public function resolveResourcePath(): string
    {
        return "payments/{$this->paymentId}/routes";
    }
}
