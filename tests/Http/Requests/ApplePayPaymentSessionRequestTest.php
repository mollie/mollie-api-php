<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\ApplePayPaymentSessionRequest;
use Mollie\Api\Resources\AnyResource;
use PHPUnit\Framework\TestCase;

class ApplePayPaymentSessionRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_apple_pay_session()
    {
        $client = new MockMollieClient([
            ApplePayPaymentSessionRequest::class => MockResponse::ok('apple-pay-session'),
        ]);

        $request = new ApplePayPaymentSessionRequest(
            'https://example.com',
            'Example Domain',
            '1234567890'
        );

        /** @var AnyResource */
        $appleSession = $client->send($request);

        $this->assertTrue($appleSession->getResponse()->successful());
        $this->assertInstanceOf(AnyResource::class, $appleSession);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new ApplePayPaymentSessionRequest(
            'https://example.com',
            'Example Domain',
            '1234567890'
        );

        $this->assertEquals(
            'wallets/applepay/sessions',
            $request->resolveResourcePath()
        );
    }
}
