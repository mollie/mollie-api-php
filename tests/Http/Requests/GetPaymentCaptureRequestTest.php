<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetPaymentCaptureRequest;
use Mollie\Api\Resources\Capture;
use PHPUnit\Framework\TestCase;

class GetPaymentCaptureRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_payment_capture()
    {
        $client = new MockMollieClient([
            GetPaymentCaptureRequest::class => MockResponse::ok('capture'),
        ]);

        $request = new GetPaymentCaptureRequest('tr_WDqYK6vllg', 'cpt_4qqhO89gsT');

        /** @var Capture */
        $capture = $client->send($request);

        $this->assertTrue($capture->getResponse()->successful());
        $this->assertInstanceOf(Capture::class, $capture);
    }

    /** @test */
    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $paymentId = 'tr_WDqYK6vllg';
        $captureId = 'cpt_4qqhO89gsT';
        $request = new GetPaymentCaptureRequest($paymentId, $captureId);

        $this->assertEquals("payments/{$paymentId}/captures/{$captureId}", $request->resolveResourcePath());
    }
}
