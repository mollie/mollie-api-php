<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\ApplePayPaymentSessionRequest;

class ApplePayPaymentSessionRequestFactory extends RequestFactory
{
    public function create(): ApplePayPaymentSessionRequest
    {
        return new ApplePayPaymentSessionRequest(
            $this->payload('domain'),
            $this->payload('validationUrl'),
            $this->payload('profileId'),
        );
    }
}
