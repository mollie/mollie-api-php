<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Resources\Capture;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/v2/captures-api/create-capture
 */
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
    protected ?string $hydratableResource = Capture::class;

    public function __construct(
        private string $paymentId,
        private string $description,
        private ?Money $amount = null,
        private ?array $metadata = null,
    ) {
    }

    protected function defaultPayload(): array
    {
        return [
            'description' => $this->description,
            'amount' => $this->amount,
            'metadata' => $this->metadata,
        ];
    }

    public function resolveResourcePath(): string
    {
        return "payments/{$this->paymentId}/captures";
    }
}
