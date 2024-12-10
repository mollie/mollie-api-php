<?php

namespace Mollie\Api\Http\Payload;

class UpdatePaymentPayload extends DataBag
{
    public ?string $description;

    public ?string $redirectUrl;

    public ?string $cancelUrl;

    public ?string $webhookUrl;

    public ?Metadata $metadata;

    public ?string $method;

    public ?string $locale;

    public ?string $restrictPaymentMethodsToCountry;

    /**
     * Method specific data.
     *
     * s. https://docs.mollie.com/reference/extra-payment-parameters#bank-transfer
     */
    public array $additional = [];

    public function __construct(
        ?string $description = null,
        ?string $redirectUrl = null,
        ?string $cancelUrl = null,
        ?string $webhookUrl = null,
        ?Metadata $metadata = null,
        ?string $method = null,
        ?string $locale = null,
        ?string $restrictPaymentMethodsToCountry = null,
        array $additional = []
    ) {
        $this->description = $description;
        $this->redirectUrl = $redirectUrl;
        $this->cancelUrl = $cancelUrl;
        $this->webhookUrl = $webhookUrl;
        $this->metadata = $metadata;
        $this->method = $method;
        $this->locale = $locale;
        $this->restrictPaymentMethodsToCountry = $restrictPaymentMethodsToCountry;
        $this->additional = $additional;
    }

    public function toArray(): array
    {
        return array_merge([
            'description' => $this->description,
            'redirectUrl' => $this->redirectUrl,
            'cancelUrl' => $this->cancelUrl,
            'webhookUrl' => $this->webhookUrl,
            'metadata' => $this->metadata,
            'method' => $this->method,
            'locale' => $this->locale,
            'restrictPaymentMethodsToCountry' => $this->restrictPaymentMethodsToCountry,
        ], $this->additional);
    }
}
