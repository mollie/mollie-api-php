<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\ApplePayPaymentSessionPayloadFactory;
use Mollie\Api\Http\Requests\ApplePayPaymentSessionRequest;

class WalletEndpointCollection extends EndpointCollection
{
    /**
     * Obtain a new ApplePay payment session.
     *
     * @param  array  $parameters  Additional parameters
     * @return string The payment session data
     *
     * @throws ApiException
     */
    public function requestApplePayPaymentSession(string $domain, string $validationUrl, array $parameters = []): string
    {
        $payload = ApplePayPaymentSessionPayloadFactory::new(array_merge([
            'domain' => $domain,
            'validationUrl' => $validationUrl,
        ], $parameters))->create();

        /** @var string */
        return $this->send(new ApplePayPaymentSessionRequest($payload));
    }
}
