<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Requests\CreatePaymentCaptureRequest;
use Mollie\Api\Http\Requests\GetPaymentCaptureRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentCapturesRequest;
use Mollie\Api\Resources\Capture;
use Mollie\Api\Resources\CaptureCollection;
use Mollie\Api\Resources\Payment;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class PaymentCaptureEndpointCollectionTest extends TestCase
{
    /** @test */
    public function create_for_test()
    {
        $client = new MockClient([
            CreatePaymentCaptureRequest::class => new MockResponse(201, 'capture'),
        ]);

        $payment = new Payment($client);
        $payment->id = 'tr_7UhSN1zuXS';

        /** @var Capture $capture */
        $capture = $client->paymentCaptures->createFor($payment, [
            'amount' => [
                'currency' => 'EUR',
                'value' => '35.95'
            ],
            'description' => 'Capture for cart #12345',
        ]);

        $this->assertCapture($capture);
    }

    /** @test */
    public function get_for_test()
    {
        $client = new MockClient([
            GetPaymentCaptureRequest::class => new MockResponse(200, 'capture'),
        ]);

        $payment = new Payment($client);
        $payment->id = 'tr_7UhSN1zuXS';

        /** @var Capture $capture */
        $capture = $client->paymentCaptures->getFor($payment, 'cpt_mNepDkEtco6ah3QNPUGYH');

        $this->assertCapture($capture);
    }

    /** @test */
    public function page_for_test()
    {
        $client = new MockClient([
            GetPaginatedPaymentCapturesRequest::class => new MockResponse(200, 'capture-list'),
        ]);

        $payment = new Payment($client);
        $payment->id = 'tr_7UhSN1zuXS';

        /** @var CaptureCollection $captures */
        $captures = $client->paymentCaptures->pageFor($payment);

        $this->assertInstanceOf(CaptureCollection::class, $captures);
        $this->assertEquals(1, $captures->count());
        $this->assertCount(1, $captures);

        $this->assertCapture($captures[0]);
    }

    /** @test */
    public function iterator_for_test()
    {
        $client = new MockClient([
            GetPaginatedPaymentCapturesRequest::class => new MockResponse(200, 'capture-list'),
        ]);

        $payment = new Payment($client);
        $payment->id = 'tr_7UhSN1zuXS';

        foreach ($client->paymentCaptures->iteratorFor($payment) as $capture) {
            $this->assertInstanceOf(Capture::class, $capture);
            $this->assertCapture($capture);
        }
    }

    protected function assertCapture(Capture $capture)
    {
        $this->assertInstanceOf(Capture::class, $capture);
        $this->assertEquals('capture', $capture->resource);
        $this->assertNotEmpty($capture->id);
        $this->assertEquals('live', $capture->mode);
        $this->assertEquals('Capture for cart #12345', $capture->description);
        $this->assertEquals('35.95', $capture->amount->value);
        $this->assertEquals('EUR', $capture->amount->currency);
        $this->assertEquals('tr_7UhSN1zuXS', $capture->paymentId);
        $this->assertNotEmpty($capture->createdAt);
        $this->assertNotEmpty($capture->_links);
    }
}
