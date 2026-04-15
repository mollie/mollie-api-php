<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/v2/payments-api/update-payment
 */
class UpdatePaymentRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInPayload
{
    use HasJsonPayload;

    protected static string $method = Method::PATCH;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = Payment::class;

    private ?string $paymentMethod;

    public function __construct(
        private string $id,
        private ?string $description = null,
        private ?string $redirectUrl = null,
        private ?string $cancelUrl = null,
        private ?string $webhookUrl = null,
        private ?array $metadata = null,
        ?string $method = null,
        private ?string $locale = null,
        private ?string $restrictPaymentMethodsToCountry = null,
        private array $additional = [],
    ) {
        $this->paymentMethod = $method;
    }

    protected function defaultPayload(): array
    {
        return array_merge([
            'description' => $this->description,
            'redirectUrl' => $this->redirectUrl,
            'cancelUrl' => $this->cancelUrl,
            'webhookUrl' => $this->webhookUrl,
            'metadata' => $this->metadata,
            'method' => $this->paymentMethod,
            'locale' => $this->locale,
            'restrictPaymentMethodsToCountry' => $this->restrictPaymentMethodsToCountry,
        ], $this->additional);
    }

    public function resolveResourcePath(): string
    {
        return "payments/{$this->id}";
    }
}
