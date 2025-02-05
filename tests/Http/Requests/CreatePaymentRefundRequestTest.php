<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreatePaymentRefundRequest;
use Mollie\Api\Resources\Refund;
use PHPUnit\Framework\TestCase;

class CreatePaymentRefundRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_payment_refund()
    {
        $client = new MockMollieClient([
            CreatePaymentRefundRequest::class => MockResponse::created('refund'),
        ]);

        $request = new CreatePaymentRefundRequest(
            'tr_WDqYK6vllg',
            'Order cancellation',
            new Money('EUR', '10.00')
        );

        /** @var Refund */
        $refund = $client->send($request);

        $this->assertTrue($refund->getResponse()->successful());
        $this->assertInstanceOf(Refund::class, $refund);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $paymentId = 'tr_WDqYK6vllg';

        $request = new CreatePaymentRefundRequest(
            $paymentId,
            'Order cancellation',
            new Money('EUR', '10.00')
        );

        $this->assertEquals(
            "payments/{$paymentId}/refunds",
            $request->resolveResourcePath()
        );
    }
}
