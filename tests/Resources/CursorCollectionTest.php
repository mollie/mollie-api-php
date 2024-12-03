<?php

namespace Tests\Resources;

use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\PaymentCollection;
use Tests\TestCase;
use stdClass;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\Fixtures\SequenceMockResponse;

class CursorCollectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        MockClient::shouldAutoHydrate();
    }

    /** @test */
    public function can_get_next_collection_result_when_next_link_is_available()
    {
        $client = new MockClient([
            DynamicGetRequest::class => new MockResponse(200, 'cursor-collection'),
        ]);

        $collection = new PaymentCollection(
            $client,
            [],
            $this->arrayToObject([
                'next' => [
                    'href' => 'https://api.mollie.com/v2/payments?from=tr_*',
                ],
            ])
        );

        $this->assertTrue($collection->hasNext());

        $nextPage = $collection->next();

        $this->assertFalse($nextPage->hasNext());
    }

    public function test_will_return_null_if_no_next_result_is_available()
    {
        $client = new MockClient;

        $collection = new PaymentCollection(
            $client,
            [],
            (object) []
        );

        $this->assertFalse($collection->hasNext());
        $this->assertNull($collection->next());
    }

    public function test_can_get_previous_collection_result_when_previous_link_is_available()
    {
        $client = new MockClient([
            DynamicGetRequest::class => new MockResponse(200, 'cursor-collection'),
        ]);

        $collection = new PaymentCollection(
            $client,
            [],
            $this->arrayToObject([
                'previous' => [
                    'href' => 'https://api.mollie.com/v2/payments?from=tr_*',
                ],
            ])
        );

        $this->assertTrue($collection->hasPrevious());

        $previousPage = $collection->previous();

        $this->assertFalse($previousPage->hasPrevious());
    }

    public function test_will_return_null_if_no_previous_result_is_available()
    {
        $client = new MockClient;

        $collection = new PaymentCollection(
            $client,
            [],
            (object) []
        );

        $this->assertFalse($collection->hasPrevious());
        $this->assertNull($collection->previous());
    }

    public function test_auto_paginator_returns_lazy_collection()
    {
        $client = new MockClient;

        $collection = new PaymentCollection(
            $client,
            [],
            (object) []
        );

        $this->assertInstanceOf(LazyCollection::class, $collection->getAutoIterator());
    }

    public function test_auto_paginator_can_handle_consecutive_calls()
    {
        $client = new MockClient([
            DynamicGetRequest::class => new SequenceMockResponse(
                new MockResponse(200, 'cursor-collection-next', 'tr_stTC2WHAuF'),
                new MockResponse(200, 'cursor-collection-next', 'tr_stTC2WHAuS'),
                new MockResponse(200, 'cursor-collection', 'tr_stTC2WHAuB')
            ),
        ]);

        $collection = new PaymentCollection(
            $client,
            [],
            $this->arrayToObject([
                'next' => [
                    'href' => 'https://api.mollie.com/v2/payments?from=tr_stTC2WHAuS',
                ],
            ])
        );

        $orderIds = [];
        foreach ($collection->getAutoIterator() as $order) {
            $orderIds[] = $order->id;
        }

        $this->assertEquals(['tr_stTC2WHAuF', 'tr_stTC2WHAuS', 'tr_stTC2WHAuB'], $orderIds);
    }

    /**
     * Convert an array to an object recursively.
     *
     * @param  mixed  $data
     * @return mixed
     */
    private function arrayToObject($data)
    {
        if (! is_array($data)) {
            return $data;
        }

        $obj = new stdClass;

        foreach ($data as $key => $value) {
            $obj->$key = $this->arrayToObject($value);
        }

        return $obj;
    }
}
