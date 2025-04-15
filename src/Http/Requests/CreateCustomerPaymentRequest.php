<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\SupportsTestmodeInPayload;
use Mollie\Api\Http\Data\Address;
use Mollie\Api\Http\Data\ApplicationFee;
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\Money;

class CreateCustomerPaymentRequest extends CreatePaymentRequest implements HasPayload, SupportsTestmodeInPayload
{
    protected string $customerId;

    public function __construct(
        string $customerId,
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
        ?array $metadata = null,
        ?string $captureMode = null,
        ?string $captureDelay = null,
        ?ApplicationFee $applicationFee = null,
        ?DataCollection $routing = null,
        ?string $sequenceType = null,
        ?string $mandateId = null,
        ?string $profileId = null,
        array $additional = [],
        bool $includeQrCode = false
    ) {
        parent::__construct(
            $description,
            $amount,
            $redirectUrl,
            $cancelUrl,
            $webhookUrl,
            $lines,
            $billingAddress,
            $shippingAddress,
            $locale,
            $method,
            $issuer,
            $restrictPaymentMethodsToCountry,
            $metadata,
            $captureMode,
            $captureDelay,
            $applicationFee,
            $routing,
            $sequenceType,
            $mandateId,
            null, // customerId is already defined through path
            $profileId,
            $additional,
            $includeQrCode
        );

        $this->customerId = $customerId;
    }

    public function resolveResourcePath(): string
    {
        return "customers/{$this->customerId}/payments";
    }
}
