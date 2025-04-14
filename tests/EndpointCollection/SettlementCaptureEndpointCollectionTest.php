<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedSettlementCapturesRequest;
use Mollie\Api\Resources\Capture;
use Mollie\Api\Resources\CaptureCollection;
use Mollie\Api\Resources\Settlement;
use PHPUnit\Framework\TestCase;

class SettlementCaptureEndpointCollectionTest extends TestCase
{
    /** @test */
    public function page_for()
    {
        $client = new MockMollieClient([
            GetPaginatedSettlementCapturesRequest::class => MockResponse::ok('capture-list'),
        ]);

        $settlement = new Settlement($client);
        $settlement->id = 'stl_jDk30akdN';

        /** @var CaptureCollection $captures */
        $captures = $client->settlementCaptures->pageFor($settlement);

        $this->assertInstanceOf(CaptureCollection::class, $captures);
        $this->assertGreaterThan(0, $captures->count());

        foreach ($captures as $capture) {
            $this->assertCapture($capture);
        }
    }

    /** @test */
    public function iterator_for()
    {
        $client = new MockMollieClient([
            GetPaginatedSettlementCapturesRequest::class => MockResponse::ok('capture-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'captures'),
        ]);

        $settlement = new Settlement($client);
        $settlement->id = 'stl_jDk30akdN';

        foreach ($client->settlementCaptures->iteratorFor($settlement) as $capture) {
            $this->assertCapture($capture);
        }
    }

    protected function assertCapture(Capture $capture)
    {
        $this->assertInstanceOf(Capture::class, $capture);
        $this->assertEquals('capture', $capture->resource);
        $this->assertNotEmpty($capture->id);
        $this->assertNotEmpty($capture->mode);
        $this->assertNotEmpty($capture->amount);
        $this->assertNotEmpty($capture->paymentId);
        $this->assertNotEmpty($capture->createdAt);
        $this->assertNotEmpty($capture->_links);
    }
}
