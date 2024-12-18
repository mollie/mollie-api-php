<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Data\CreatePaymentCapturePayload;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreatePaymentCaptureRequest;
use Mollie\Api\Resources\Capture;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class CreatePaymentCaptureRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_payment_capture()
    {
        $client = new MockClient([
            CreatePaymentCaptureRequest::class => new MockResponse(201, 'capture'),
        ]);

        $payload = new CreatePaymentCapturePayload(
            'Test capture',
            new Money('EUR', '10.00')
        );

        $request = new CreatePaymentCaptureRequest('tr_123', $payload);

        /** @var Capture */
        $capture = $client->send($request);

        $this->assertTrue($capture->getResponse()->successful());
        $this->assertInstanceOf(Capture::class, $capture);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new CreatePaymentCaptureRequest('tr_123', new CreatePaymentCapturePayload(
            'Test capture',
            new Money('EUR', '10.00')
        ));

        $this->assertEquals('payments/tr_123/captures', $request->resolveResourcePath());
    }
}
