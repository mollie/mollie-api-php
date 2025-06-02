<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Traits\HasJsonPayload;
use Mollie\Api\Types\Method;

class UpdatePaymentRequest extends ResourceHydratableRequest implements HasPayload, SupportsTestmodeInPayload
{
    use HasJsonPayload;

    protected static string $method = Method::PATCH;

    /**
     * The resource class the request should be casted to.
     */
    protected $hydratableResource = Payment::class;

    private string $id;

    private ?string $description;

    private ?string $redirectUrl;

    private ?string $cancelUrl;

    private ?string $webhookUrl;

    private ?array $metadata;

    private ?string $paymentMethod;

    private ?string $locale;

    private ?string $restrictPaymentMethodsToCountry;

    /**
     * Method specific data.
     *
     * s. https://docs.mollie.com/reference/extra-payment-parameters#bank-transfer
     */
    private array $additional = [];

    public function __construct(
        string $id,
        ?string $description = null,
        ?string $redirectUrl = null,
        ?string $cancelUrl = null,
        ?string $webhookUrl = null,
        ?array $metadata = null,
        ?string $method = null,
        ?string $locale = null,
        ?string $restrictPaymentMethodsToCountry = null,
        array $additional = []
    ) {
        $this->id = $id;
        $this->description = $description;
        $this->redirectUrl = $redirectUrl;
        $this->cancelUrl = $cancelUrl;
        $this->webhookUrl = $webhookUrl;
        $this->metadata = $metadata;
        $this->paymentMethod = $method;
        $this->locale = $locale;
        $this->restrictPaymentMethodsToCountry = $restrictPaymentMethodsToCountry;
        $this->additional = $additional;
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
