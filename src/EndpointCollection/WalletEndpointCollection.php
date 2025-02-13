<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Factories\ApplePayPaymentSessionRequestFactory;
use Mollie\Api\Resources\AnyResource;

class WalletEndpointCollection extends EndpointCollection
{
    /**
     * Obtain a new ApplePay payment session.
     *
     * @param  array  $parameters  Additional parameters
     * @return AnyResource The payment session data
     *
     * @throws RequestException
     */
    public function requestApplePayPaymentSession(string $domain, string $validationUrl, array $parameters = []): AnyResource
    {
        $request = ApplePayPaymentSessionRequestFactory::new()
            ->withPayload(array_merge([
                'domain' => $domain,
                'validationUrl' => $validationUrl,
            ], $parameters))
            ->create();

        return $this->send($request);
    }
}
