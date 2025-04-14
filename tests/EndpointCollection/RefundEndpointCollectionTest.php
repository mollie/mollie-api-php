<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedRefundsRequest;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\RefundCollection;
use PHPUnit\Framework\TestCase;

class RefundEndpointCollectionTest extends TestCase
{
    /** @test */
    public function page()
    {
        $client = new MockMollieClient([
            GetPaginatedRefundsRequest::class => MockResponse::ok('refund-list'),
        ]);

        /** @var RefundCollection $refunds */
        $refunds = $client->refunds->page();

        $this->assertInstanceOf(RefundCollection::class, $refunds);
        $this->assertGreaterThan(0, $refunds->count());

        foreach ($refunds as $refund) {
            $this->assertRefund($refund);
        }
    }

    /** @test */
    public function iterator()
    {
        $client = new MockMollieClient([
            GetPaginatedRefundsRequest::class => MockResponse::ok('refund-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'refunds'),
        ]);

        foreach ($client->refunds->iterator() as $refund) {
            $this->assertRefund($refund);
        }
    }

    protected function assertRefund(Refund $refund)
    {
        $this->assertInstanceOf(Refund::class, $refund);
        $this->assertEquals('refund', $refund->resource);
        $this->assertNotEmpty($refund->id);
        $this->assertNotEmpty($refund->amount);
        $this->assertNotEmpty($refund->status);
        $this->assertNotEmpty($refund->createdAt);
        $this->assertNotEmpty($refund->paymentId);
        $this->assertNotEmpty($refund->_links);
    }
}
