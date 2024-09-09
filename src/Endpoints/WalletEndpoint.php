<?php

namespace Mollie\Api\Endpoints;

class WalletEndpoint extends BaseEndpoint
{
    /**
     * Obtain a new ApplePay payment session.
     *
     * @param string $domain
     * @param string $validationUrl
     * @param array $parameters
     *
     * @return false|string
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function requestApplePayPaymentSession($domain, $validationUrl, $parameters = [])
    {
        $body = $this->parseRequestBody(array_merge([
            'domain' => $domain,
            'validationUrl' => $validationUrl,
        ], $parameters));

        $response = $this->client->performHttpCall(
            self::REST_CREATE,
            'wallets/applepay/sessions',
            $body
        );

        return $response->body();
    }
}
