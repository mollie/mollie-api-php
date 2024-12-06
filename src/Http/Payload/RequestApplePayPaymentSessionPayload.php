<?php

namespace Mollie\Api\Http\Payload;

class RequestApplePayPaymentSessionPayload extends DataBag
{
    public string $domain;

    public string $validationUrl;

    public ?string $profileId = null;

    public function __construct(
        string $domain,
        string $validationUrl,
        ?string $profileId = null
    ) {
        $this->domain = $domain;
        $this->validationUrl = $validationUrl;
        $this->profileId = $profileId;
    }

    public function data(): array
    {
        return [
            'domain' => $this->domain,
            'validationUrl' => $this->validationUrl,
            'profileId' => $this->profileId,
        ];
    }
}
