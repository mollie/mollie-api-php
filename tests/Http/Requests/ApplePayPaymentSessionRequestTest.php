<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Data\RequestApplePayPaymentSessionPayload;
use Mollie\Api\Http\Requests\ApplePayPaymentSessionRequest;
use Mollie\Api\Resources\AnyResource;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class ApplePayPaymentSessionRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_apple_pay_session()
    {
        $client = new MockClient([
            ApplePayPaymentSessionRequest::class => new MockResponse(200, 'apple-pay-session'),
        ]);

        $payload = new RequestApplePayPaymentSessionPayload(
            'https://example.com',
            'Example Domain',
            'EUR'
        );

        $request = new ApplePayPaymentSessionRequest($payload);

        /** @var AnyResource */
        $appleSession = $client->send($request);

        $this->assertTrue($appleSession->getResponse()->successful());
        $this->assertInstanceOf(AnyResource::class, $appleSession);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $payload = new RequestApplePayPaymentSessionPayload(
            'https://example.com',
            'Example Domain',
            'EUR'
        );

        $request = new ApplePayPaymentSessionRequest($payload);

        $this->assertEquals(
            'wallets/applepay/sessions',
            $request->resolveResourcePath()
        );
    }
}
