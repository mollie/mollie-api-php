<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\BaseResource;

class WalletEndpoint extends EndpointAbstract
{
    /**
     * Get the object that is used by this API endpoint. Every API endpoint uses one type of object.
     *
     * @return BaseResource
     */
    protected function getResourceObject()
    {
        // Not used
    }

    public function requestApplePayPaymentSession($domain, $validationUrl)
    {
        $body = $this->parseRequestBody([
            'domain' => $domain,
            'validationUrl' => $validationUrl,
        ]);

        $response = $this->client->performHttpCall(
            self::REST_CREATE,
            'wallets/applepay/sessions',
            $body
        );

        return json_encode($response);
    }
}
