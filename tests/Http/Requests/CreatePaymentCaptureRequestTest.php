<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreatePaymentCaptureRequest;
use Mollie\Api\Resources\Capture;
use PHPUnit\Framework\TestCase;

class CreatePaymentCaptureRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_payment_capture()
    {
        $client = new MockMollieClient([
            CreatePaymentCaptureRequest::class => MockResponse::created('capture'),
        ]);

        $request = new CreatePaymentCaptureRequest(
            'tr_123',
            'Test capture',
            new Money('EUR', '10.00')
        );

        /** @var Capture */
        $capture = $client->send($request);

        $this->assertTrue($capture->getResponse()->successful());
        $this->assertInstanceOf(Capture::class, $capture);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new CreatePaymentCaptureRequest(
            'tr_123',
            'Test capture',
            new Money('EUR', '10.00')
        );

        $this->assertEquals('payments/tr_123/captures', $request->resolveResourcePath());
    }
}
