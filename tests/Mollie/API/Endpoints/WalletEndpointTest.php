<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class WalletEndpointTest extends BaseEndpointTest
{
    public function testRequestApplePayPaymentSession()
    {
        $responseBody = '{
            "epochTimestamp": 1555507053169,
            "expiresAt": 1555510653169,
            "merchantSessionIdentifier": "SSH2EAF8AFAEAA94DEEA898162A5DAFD36E_916523AAED1343F5BC5815E12BEE9250AFFDC1A17C46B0DE5A943F0F94927C24",
            "nonce": "0206b8db",
            "merchantIdentifier": "BD62FEB196874511C22DB28A9E14A89E3534C93194F73EA417EC566368D391EB",
            "domainName": "pay.example.org",
            "displayName": "Chuck Norris\'s Store",
            "signature": "308006092a864886f7...8cc030ad3000000000000"
        }';

        $this->mockApiCall(
            new Request(
                "POST",
                "/v2/wallets/applepay/sessions",
                [],
                '{
                    "domain": "pay.mywebshop.com",
                    "validationUrl": "https://apple-pay-gateway-cert.apple.com/paymentservices/paymentSession"
                }'
            ),
            new Response(
                201,
                [],
                $responseBody
            )
        );

        $domain = 'pay.mywebshop.com';
        $validationUrl = 'https://apple-pay-gateway-cert.apple.com/paymentservices/paymentSession';

        $response = $this->apiClient->wallets->requestApplePayPaymentSession($domain, $validationUrl);

        $this->assertJsonStringEqualsJsonString(
            $responseBody,
            $response
        );
    }
}
