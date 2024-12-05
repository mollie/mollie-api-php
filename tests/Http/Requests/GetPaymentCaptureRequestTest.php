<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetPaymentCaptureRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Capture;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetPaymentCaptureRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_payment_capture()
    {
        $client = new MockClient([
            GetPaymentCaptureRequest::class => new MockResponse(200, 'capture'),
        ]);

        $request = new GetPaymentCaptureRequest('tr_WDqYK6vllg', 'cpt_4qqhO89gsT');

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());

        /** @var Capture */
        $capture = $response->toResource();

        $this->assertInstanceOf(Capture::class, $capture);
        $this->assertEquals('capture', $capture->resource);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $paymentId = 'tr_WDqYK6vllg';
        $captureId = 'cpt_4qqhO89gsT';
        $request = new GetPaymentCaptureRequest($paymentId, $captureId);

        $this->assertEquals("payments/{$paymentId}/captures/{$captureId}", $request->resolveResourcePath());
    }
}
