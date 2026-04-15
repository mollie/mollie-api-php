<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/v2/refunds-api/create-refund
 */
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
    protected ?string $hydratableResource = Refund::class;

    private Money $amount;

    public function __construct(
        private string $paymentId,
        private string $description = '',
        ?Money $amount = null,
        private ?array $metadata = null,
        private ?bool $reverseRouting = null,
        private ?DataCollection $routingReversals = null,
    ) {
        if ($amount === null) {
            throw new \InvalidArgumentException('The amount parameter is required.');
        }

        $this->amount = $amount;
    }

    /**
     * Create a refund request with correct parameter ordering.
     * This factory method avoids PHP 8+ deprecation warnings when using positional arguments.
     *
     * @param string $paymentId The payment ID to refund
     * @param Money $amount The amount to refund
     * @param string $description Optional description for the refund
     * @param array|null $metadata Optional metadata
     * @param bool|null $reverseRouting Optional reverse routing flag
     * @param DataCollection|null $routingReversals Optional routing reversals
     * @return self
     */
    public static function for(
        string $paymentId,
        Money $amount,
        string $description = '',
        ?array $metadata = null,
        ?bool $reverseRouting = null,
        ?DataCollection $routingReversals = null
    ): self {
        return new self(
            $paymentId,
            $description,
            $amount,
            $metadata,
            $reverseRouting,
            $routingReversals
        );
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
