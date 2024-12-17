<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Data\UpdatePaymentPayload;
use Mollie\Api\Http\Requests\UpdatePaymentRequest;
use Mollie\Api\Resources\Payment;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class UpdatePaymentRequestTest extends TestCase
{
    /** @test */
    public function it_can_update_payment()
    {
        $client = new MockClient([
            UpdatePaymentRequest::class => new MockResponse(200, 'payment'),
        ]);

        $request = new UpdatePaymentRequest('tr_WDqYK6vllg', new UpdatePaymentPayload(
            'Updated payment description',
            'https://example.com/redirect',
        ));

        /** @var Payment */
        $payment = $client->send($request);

        $this->assertTrue($payment->getResponse()->successful());
        $this->assertInstanceOf(Payment::class, $payment);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new UpdatePaymentRequest('tr_WDqYK6vllg', new UpdatePaymentPayload(
            'Updated payment description',
            'https://example.com/redirect',
        ));

        $this->assertEquals('payments/tr_WDqYK6vllg', $request->resolveResourcePath());
    }
}
