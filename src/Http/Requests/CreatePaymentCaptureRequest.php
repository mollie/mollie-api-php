<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Data\Money;
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
    protected $hydratableResource = Capture::class;

    private string $paymentId;

    private string $description;

    private ?Money $amount;

    private ?array $metadata;

    public function __construct(
        string $paymentId,
        string $description,
        ?Money $amount = null,
        ?array $metadata = null
    ) {
        $this->paymentId = $paymentId;
        $this->description = $description;
        $this->amount = $amount;
        $this->metadata = $metadata;
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
