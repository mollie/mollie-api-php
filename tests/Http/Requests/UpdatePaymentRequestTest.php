<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\UpdatePaymentRequest;
use Mollie\Api\Resources\Payment;
use PHPUnit\Framework\TestCase;

class UpdatePaymentRequestTest extends TestCase
{
    /** @test */
    public function it_can_update_payment()
    {
        $client = new MockMollieClient([
            UpdatePaymentRequest::class => MockResponse::ok('payment'),
        ]);

        $request = new UpdatePaymentRequest(
            'tr_WDqYK6vllg',
            'Updated payment description',
            'https://example.com/redirect',
        );

        /** @var Payment */
        $payment = $client->send($request);

        $this->assertTrue($payment->getResponse()->successful());
        $this->assertInstanceOf(Payment::class, $payment);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new UpdatePaymentRequest(
            'tr_WDqYK6vllg',
            'Updated payment description',
            'https://example.com/redirect',
        );

        $this->assertEquals('payments/tr_WDqYK6vllg', $request->resolveResourcePath());
    }
}
