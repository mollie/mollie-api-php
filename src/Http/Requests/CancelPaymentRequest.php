<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/v2/payments-api/cancel-payment
 *
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\Payment>
 */
class CancelPaymentRequest extends ResourceHydratableRequest implements SupportsTestmodeInPayload
{
    use HasJsonPayload;

    protected static string $method = Method::DELETE;

    protected ?string $hydratableResource = Payment::class;

    public function __construct(
        private string $id,
    ) {
    }

    public function resolveResourcePath(): string
    {
        return "payments/{$this->id}";
    }
}
