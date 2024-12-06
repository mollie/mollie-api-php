<?php

namespace Mollie\Api\Http\Payload;

class CreatePaymentPayload extends DataBag
{
    public string $description;

    public Money $amount;

    public ?string $redirectUrl;

    public ?string $cancelUrl;

    public ?string $webhookUrl;

    /**
     * @var DataCollection<OrderLine>|null
     */
    public ?DataCollection $lines;

    public ?Address $billingAddress;

    public ?Address $shippingAddress;

    public ?string $locale;

    public ?string $method;

    public ?string $issuer;

    public ?string $restrictPaymentMethodsToCountry;

    public ?Metadata $metadata;

    public ?string $captureMode;

    public ?string $captureDelay;

    public ?ApplicationFee $applicationFee;

    /**
     * @var DataCollection<PaymentRoute>|null
     */
    public ?DataCollection $routing;

    public ?string $sequenceType;

    public ?string $mandateId;

    public ?string $customerId;

    public ?string $profileId;

    /**
     * Method specific data.
     *
     * s. https://docs.mollie.com/reference/extra-payment-parameters#payment-creation-request-parameters
     */
    public array $additional = [];

    public function __construct(
        string $description,
        Money $amount,
        ?string $redirectUrl = null,
        ?string $cancelUrl = null,
        ?string $webhookUrl = null,
        ?DataCollection $lines = null,
        ?Address $billingAddress = null,
        ?Address $shippingAddress = null,
        ?string $locale = null,
        ?string $method = null,
        ?string $issuer = null,
        ?string $restrictPaymentMethodsToCountry = null,
        ?Metadata $metadata = null,
        ?string $captureMode = null,
        ?string $captureDelay = null,
        ?ApplicationFee $applicationFee = null,
        ?DataCollection $routing = null,
        ?string $sequenceType = null,
        ?string $mandateId = null,
        ?string $customerId = null,
        ?string $profileId = null,
        array $additional = []
    ) {
        $this->description = $description;
        $this->amount = $amount;
        $this->redirectUrl = $redirectUrl;
        $this->cancelUrl = $cancelUrl;
        $this->webhookUrl = $webhookUrl;
        $this->lines = $lines;
        $this->billingAddress = $billingAddress;
        $this->shippingAddress = $shippingAddress;
        $this->locale = $locale;
        $this->method = $method;
        $this->issuer = $issuer;
        $this->restrictPaymentMethodsToCountry = $restrictPaymentMethodsToCountry;
        $this->metadata = $metadata;
        $this->captureMode = $captureMode;
        $this->captureDelay = $captureDelay;
        $this->applicationFee = $applicationFee;
        $this->routing = $routing;
        $this->sequenceType = $sequenceType;
        $this->mandateId = $mandateId;
        $this->customerId = $customerId;
        $this->profileId = $profileId;
        $this->additional = $additional;
    }

    public function data(): array
    {
        return array_merge([
            'description' => $this->description,
            'amount' => $this->amount,
            'redirectUrl' => $this->redirectUrl,
            'cancelUrl' => $this->cancelUrl,
            'webhookUrl' => $this->webhookUrl,
            'lines' => $this->lines,
            'billingAddress' => $this->billingAddress,
            'shippingAddress' => $this->shippingAddress,
            'locale' => $this->locale,
            'method' => $this->method,
            'issuer' => $this->issuer,
            'restrictPaymentMethodsToCountry' => $this->restrictPaymentMethodsToCountry,
            'metadata' => $this->metadata,
            'captureMode' => $this->captureMode,
            'captureDelay' => $this->captureDelay,
            'applicationFee' => $this->applicationFee,
            'routing' => $this->routing,
            'sequenceType' => $this->sequenceType,
            'mandateId' => $this->mandateId,
            'customerId' => $this->customerId,
            'profileId' => $this->profileId,
        ], $this->additional);
    }
}
