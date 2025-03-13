<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetPaymentChargebackRequest;
use Mollie\Api\Resources\Chargeback;
use PHPUnit\Framework\TestCase;

class GetPaymentChargebackRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_payment_chargeback()
    {
        $client = new MockMollieClient([
            GetPaymentChargebackRequest::class => MockResponse::ok('chargeback'),
        ]);

        $request = new GetPaymentChargebackRequest('tr_WDqYK6vllg', 'chb_n9z0tp');

        /** @var Chargeback */
        $chargeback = $client->send($request);

        $this->assertTrue($chargeback->getResponse()->successful());
        $this->assertInstanceOf(Chargeback::class, $chargeback);
    }

    /** @test */
    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $paymentId = 'tr_WDqYK6vllg';
        $chargebackId = 'chb_n9z0tp';
        $request = new GetPaymentChargebackRequest($paymentId, $chargebackId);

        $this->assertEquals("payments/{$paymentId}/chargebacks/{$chargebackId}", $request->resolveResourcePath());
    }
}
