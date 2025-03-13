<?php

namespace Tests\Resources;

use Mollie\Api\Contracts\Connector;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\BaseCollection;
use PHPUnit\Framework\TestCase;
use stdClass;

class BaseCollectionTest extends TestCase
{
    private Connector $connectorMock;

    private Response $response;

    protected function setUp(): void
    {
        $this->connectorMock = $this->createMock(Connector::class);
        $this->response = $this->createMock(Response::class);
    }

    /** @test */
    public function constructor_initializes_collection_properly()
    {
        $items = ['item1', 'item2'];
        $links = new stdClass;
        $links->self = 'https://api.mollie.com/v2/test';

        $collection = new TestCollection($this->connectorMock, $items, $links);

        $collection->setResponse($this->response);

        $this->assertCount(2, $collection);
        $this->assertSame($items, $collection->getArrayCopy());
        $this->assertSame($links, $collection->_links);
    }

    /** @test */
    public function contains_returns_true_when_item_exists()
    {
        $items = [
            'apple',
            'banana',
            'orange',
        ];

        $collection = new TestCollection($this->connectorMock, $items);
        $collection->setResponse($this->response);

        $this->assertTrue($collection->contains(fn ($item) => $item === 'banana'));
    }

    /** @test */
    public function contains_returns_false_when_item_does_not_exist()
    {
        $items = [
            'apple',
            'banana',
            'orange',
        ];

        $collection = new TestCollection($this->connectorMock, $items);
        $collection->setResponse($this->response);
        $this->assertFalse($collection->contains(fn ($item) => $item === 'grape'));
    }

    /** @test */
    public function filter_returns_filtered_collection()
    {
        $items = [
            'apple',
            'banana',
            'orange',
        ];

        $collection = new TestCollection($this->connectorMock, $items);
        $collection->setResponse($this->response);
        $filtered = $collection->filter(fn ($item) => $item !== 'banana');

        $this->assertCount(2, $filtered);
        $this->assertContains('apple', $filtered);
        $this->assertContains('orange', $filtered);
        $this->assertNotContains('banana', $filtered);

        // Verify it returns a new collection instance
        $this->assertInstanceOf(TestCollection::class, $filtered);
        $this->assertNotSame($collection, $filtered);
    }

    /** @test */
    public function first_returns_first_item()
    {
        $collection = new TestCollection($this->connectorMock, ['item1', 'item2']);
        $collection->setResponse($this->response);
        $this->assertEquals('item1', $collection->first());
    }

    /** @test */
    public function first_where_returns_first_item_where_condition_is_true()
    {
        $collection = new TestCollection($this->connectorMock, [
            ['id' => 'item1'],
            ['id' => 'item2'],
        ]);
        $collection->setResponse($this->response);
        $this->assertEquals(['id' => 'item2'], $collection->firstWhere('id', 'item2'));
        $this->assertEquals(['id' => 'item1'], $collection->firstWhere(fn ($item) => $item['id'] === 'item1'));
    }

    /** @test */
    public function get_collection_resource_name_returns_name()
    {
        $this->assertEquals('test_collection', TestCollection::getCollectionResourceName());
    }

    /** @test */
    public function get_collection_resource_name_throws_exception_when_empty()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Collection name not set');

        EmptyCollection::getCollectionResourceName();
    }
}

class TestCollection extends BaseCollection
{
    public static string $collectionName = 'test_collection';
}

class EmptyCollection extends BaseCollection
{
    // Intentionally empty collection name
}
