<?php

namespace Mollie\Api\Http\Payload;

use DateTimeInterface;

class CreateMandatePayload extends DataBag
{
    public string $method;

    public string $consumerName;

    public ?string $consumerAccount;

    public ?string $consumerBic;

    public ?string $consumerEmail;

    public ?DateTimeInterface $signatureDate;

    public ?string $mandateReference;

    public ?string $paypalBillingAgreementId;

    public function __construct(
        string $method,
        string $consumerName,
        ?string $consumerAccount = null,
        ?string $consumerBic = null,
        ?string $consumerEmail = null,
        ?DateTimeInterface $signatureDate = null,
        ?string $mandateReference = null,
        ?string $paypalBillingAgreementId = null
    ) {
        $this->method = $method;
        $this->consumerName = $consumerName;
        $this->consumerAccount = $consumerAccount;
        $this->consumerBic = $consumerBic;
        $this->consumerEmail = $consumerEmail;
        $this->signatureDate = $signatureDate;
        $this->mandateReference = $mandateReference;
        $this->paypalBillingAgreementId = $paypalBillingAgreementId;
    }

    public function data(): array
    {
        return [
            'method' => $this->method,
            'consumerName' => $this->consumerName,
            'consumerAccount' => $this->consumerAccount,
            'consumerBic' => $this->consumerBic,
            'consumerEmail' => $this->consumerEmail,
            'signatureDate' => $this->signatureDate?->format('Y-m-d'),
            'mandateReference' => $this->mandateReference,
            'paypalBillingAgreementId' => $this->paypalBillingAgreementId,
        ];
    }
}
