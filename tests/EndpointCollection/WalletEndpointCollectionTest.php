<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Requests\ApplePayPaymentSessionRequest;
use Mollie\Api\Resources\AnyResource;
use Tests\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class WalletEndpointCollectionTest extends TestCase
{
    /** @test */
    public function request_apple_pay_payment_session()
    {
        $client = new MockClient([
            ApplePayPaymentSessionRequest::class => new MockResponse(200, 'apple-pay-session'),
        ]);

        $applePaySession = $client->wallets->requestApplePayPaymentSession(
            'pay.example.org',
            'https://apple-pay-gateway.example.com/paymentservices/paymentSession',
            [
                'displayName' => "Chuck Norris's Store",
            ]
        );

        $this->assertInstanceOf(AnyResource::class, $applePaySession);
        $this->assertNotEmpty($applePaySession->domainName);
        $this->assertNotEmpty($applePaySession->displayName);
        $this->assertNotEmpty($applePaySession->merchantIdentifier);
        $this->assertNotEmpty($applePaySession->merchantSessionIdentifier);
        $this->assertNotEmpty($applePaySession->nonce);
        $this->assertNotEmpty($applePaySession->signature);
    }
}
