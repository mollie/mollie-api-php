<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class CreatePaymentRefundRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInPayload
{
    use HasJsonPayload;

    /**
     * Define the HTTP method.
     */
    protected static string $method = Method::POST;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = Refund::class;

    private string $paymentId;

    private string $description;

    private Money $amount;

    private ?array $metadata;

    private ?bool $reverseRouting;

    private ?DataCollection $routingReversals;

    public function __construct(
        string $paymentId,
        string $description,
        Money $amount,
        ?array $metadata = null,
        ?bool $reverseRouting = null,
        ?DataCollection $routingReversals = null
    ) {
        $this->paymentId = $paymentId;
        $this->description = $description;
        $this->amount = $amount;
        $this->metadata = $metadata;
        $this->reverseRouting = $reverseRouting;
        $this->routingReversals = $routingReversals;
    }

    protected function defaultPayload(): array
    {
        return [
            'description' => $this->description,
            'amount' => $this->amount,
            'metadata' => $this->metadata,
            'reverseRouting' => $this->reverseRouting,
            'routingReversals' => $this->routingReversals,
        ];
    }

    public function resolveResourcePath(): string
    {
        return "payments/{$this->paymentId}/refunds";
    }
}
