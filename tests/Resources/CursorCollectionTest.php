<?php

namespace Tests\Resources;

use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\OrderCollection;
use PHPUnit\Framework\TestCase;
use stdClass;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\Fixtures\SequenceMockResponse;

class CursorCollectionTest extends TestCase
{
    /** @test */
    public function can_get_next_collection_result_when_next_link_is_available()
    {
        $client = new MockClient([
            DynamicGetRequest::class => new MockResponse(200, 'cursor-collection', 'ord_stTC2WHAuS'),
        ]);

        $collection = new OrderCollection(
            $client,
            [],
            $this->arrayToObject([
                'next' => [
                    'href' => 'https://api.mollie.com/v2/orders?from=ord_stTC2WHAuS',
                ],
            ])
        );

        $this->assertTrue($collection->hasNext());

        $nextPage = $collection->next();

        $this->assertEquals('ord_stTC2WHAuS', $nextPage[0]->id);

        $this->assertFalse($nextPage->hasNext());
    }

    public function testWillReturnNullIfNoNextResultIsAvailable()
    {
        $client = new MockClient;

        $collection = new OrderCollection(
            $client,
            [],
            (object) []
        );

        $this->assertFalse($collection->hasNext());
        $this->assertNull($collection->next());
    }

    public function testCanGetPreviousCollectionResultWhenPreviousLinkIsAvailable()
    {
        $client = new MockClient([
            DynamicGetRequest::class => new MockResponse(200, 'cursor-collection', 'ord_stTC2WHAuS'),
        ]);

        $collection = new OrderCollection(
            $client,
            [],
            $this->arrayToObject([
                'previous' => [
                    'href' => 'https://api.mollie.com/v2/orders?from=ord_stTC2WHAuS',
                ],
            ])
        );

        $this->assertTrue($collection->hasPrevious());

        $previousPage = $collection->previous();

        $this->assertEquals('ord_stTC2WHAuS', $previousPage[0]->id);

        $this->assertFalse($previousPage->hasPrevious());
    }

    public function testWillReturnNullIfNoPreviousResultIsAvailable()
    {
        $client = new MockClient;

        $collection = new OrderCollection(
            $client,
            [],
            (object) []
        );

        $this->assertFalse($collection->hasPrevious());
        $this->assertNull($collection->previous());
    }

    public function testAutoPaginatorReturnsLazyCollection()
    {
        $client = new MockClient;

        $collection = new OrderCollection(
            $client,
            [],
            (object) []
        );

        $this->assertInstanceOf(LazyCollection::class, $collection->getAutoIterator());
    }

    public function testAutoPaginatorCanHandleConsecutiveCalls()
    {
        $client = new MockClient([
            DynamicGetRequest::class => new SequenceMockResponse(
                new MockResponse(200, 'cursor-collection-next', 'ord_stTC2WHAuF'),
                new MockResponse(200, 'cursor-collection-next', 'ord_stTC2WHAuS'),
                new MockResponse(200, 'cursor-collection', 'ord_stTC2WHAuB')
            ),
        ]);

        $collection = new OrderCollection(
            $client,
            [],
            $this->arrayToObject([
                'next' => [
                    'href' => 'https://api.mollie.com/v2/orders?from=ord_stTC2WHAuS',
                ],
            ])
        );

        $orderIds = [];
        foreach ($collection->getAutoIterator() as $order) {
            $orderIds[] = $order->id;
        }

        $this->assertEquals(['ord_stTC2WHAuF', 'ord_stTC2WHAuS', 'ord_stTC2WHAuB'], $orderIds);
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
