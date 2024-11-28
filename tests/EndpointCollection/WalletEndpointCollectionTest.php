<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Requests\ApplePayPaymentSessionRequest;
use PHPUnit\Framework\TestCase;
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

        $session = $client->wallets->requestApplePayPaymentSession(
            'pay.example.org',
            'https://apple-pay-gateway.example.com/paymentservices/paymentSession',
            [
                'displayName' => "Chuck Norris's Store",
            ]
        );

        $this->assertIsString($session);
        $this->assertNotEmpty($session);
    }
}
