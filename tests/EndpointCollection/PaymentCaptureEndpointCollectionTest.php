<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreatePaymentCaptureRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentCapturesRequest;
use Mollie\Api\Http\Requests\GetPaymentCaptureRequest;
use Mollie\Api\Resources\Capture;
use Mollie\Api\Resources\CaptureCollection;
use PHPUnit\Framework\TestCase;

class PaymentCaptureEndpointCollectionTest extends TestCase
{
    /** @test */
    public function create_for_id()
    {
        $client = new MockMollieClient([
            CreatePaymentCaptureRequest::class => MockResponse::created('capture'),
        ]);

        /** @var Capture $capture */
        $capture = $client->paymentCaptures->createForId('tr_7UhSN1zuXS', [
            'description' => 'Capture for cart #12345',
            'amount' => new Money('EUR', '35.95'),
        ]);

        $this->assertCapture($capture);
    }

    /** @test */
    public function get_for_id()
    {
        $client = new MockMollieClient([
            GetPaymentCaptureRequest::class => MockResponse::ok('capture'),
        ]);

        /** @var Capture $capture */
        $capture = $client->paymentCaptures->getForId('tr_7UhSN1zuXS', 'cpt_mNepDkEtco6ah3QNPUGYH');

        $this->assertCapture($capture);
    }

    /** @test */
    public function page_for_id()
    {
        $client = new MockMollieClient([
            GetPaginatedPaymentCapturesRequest::class => MockResponse::ok('capture-list'),
        ]);

        /** @var CaptureCollection $captures */
        $captures = $client->paymentCaptures->pageForId('tr_7UhSN1zuXS');

        $this->assertInstanceOf(CaptureCollection::class, $captures);
        $this->assertEquals(1, $captures->count());
        $this->assertCount(1, $captures);

        $this->assertCapture($captures[0]);
    }

    /** @test */
    public function iterator_for_id()
    {
        $client = new MockMollieClient([
            GetPaginatedPaymentCapturesRequest::class => MockResponse::ok('capture-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'captures'),
        ]);

        foreach ($client->paymentCaptures->iteratorForId('tr_7UhSN1zuXS') as $capture) {
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
