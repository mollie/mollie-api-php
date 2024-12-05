<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetPaymentChargebackRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Chargeback;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetPaymentChargebackRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_payment_chargeback()
    {
        $client = new MockClient([
            GetPaymentChargebackRequest::class => new MockResponse(200, 'chargeback'),
        ]);

        $request = new GetPaymentChargebackRequest('tr_WDqYK6vllg', 'chb_n9z0tp');

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());

        /** @var Chargeback */
        $chargeback = $response->toResource();

        $this->assertInstanceOf(Chargeback::class, $chargeback);
        $this->assertEquals('chargeback', $chargeback->resource);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $paymentId = 'tr_WDqYK6vllg';
        $chargebackId = 'chb_n9z0tp';
        $request = new GetPaymentChargebackRequest($paymentId, $chargebackId);

        $this->assertEquals("payments/{$paymentId}/chargebacks/{$chargebackId}", $request->resolveResourcePath());
    }
}
