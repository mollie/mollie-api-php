<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\ApplePayPaymentSessionRequest;
use Mollie\Api\Resources\AnyResource;
use PHPUnit\Framework\TestCase;

class WalletEndpointCollectionTest extends TestCase
{
    /** @test */
    public function request_apple_pay_payment_session()
    {
        $client = new MockMollieClient([
            ApplePayPaymentSessionRequest::class => MockResponse::ok('apple-pay-session'),
        ]);

        $applePaySession = $client->wallets->requestApplePayPaymentSession(
            'pay.example.org',
            'https://apple-pay-gateway.example.com/paymentservices/paymentSession',
            [
                'displayName' => "Chuck Norris's Store",
            ]
        );

        $this->assertInstanceOf(AnyResource::class, $applePaySession);
        /** @phpstan-ignore-next-line */
        $this->assertNotEmpty($applePaySession->domainName);
        /** @phpstan-ignore-next-line */
        $this->assertNotEmpty($applePaySession->displayName);
        /** @phpstan-ignore-next-line */
        $this->assertNotEmpty($applePaySession->merchantIdentifier);
        /** @phpstan-ignore-next-line */
        $this->assertNotEmpty($applePaySession->merchantSessionIdentifier);
        /** @phpstan-ignore-next-line */
        $this->assertNotEmpty($applePaySession->nonce);
        /** @phpstan-ignore-next-line */
        $this->assertNotEmpty($applePaySession->signature);
    }
}
