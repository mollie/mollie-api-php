<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Payload\RequestApplePayPaymentSessionPayload;

class ApplePayPaymentSessionPayloadFactory extends Factory
{
    public function create(): RequestApplePayPaymentSessionPayload
    {
        return new RequestApplePayPaymentSessionPayload(
            $this->get('domain'),
            $this->get('validationUrl'),
            $this->get('profileId'),
        );
    }
}
