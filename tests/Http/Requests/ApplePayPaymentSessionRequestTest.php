<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Data\RequestApplePayPaymentSessionPayload;
use Mollie\Api\Http\Requests\ApplePayPaymentSessionRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\AnyResource;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

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

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(AnyResource::class, $response->toResource());
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
