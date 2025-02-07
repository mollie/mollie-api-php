<?php

namespace Tests\Resources;

use Mollie\Api\Http\Response;
use Mollie\Api\Resources\LazyCollection;
use PHPUnit\Framework\TestCase;

class LazyCollectionTest extends TestCase
{
    /**
     * @var LazyCollection
     */
    private $collection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->collection = new LazyCollection(function () {
            yield 1;
            yield 2;
            yield 3;
        });

        $response = $this->createMock(Response::class);

        $this->collection->setResponse($response);
    }

    /** @test */
    public function can_create_a_collection_from_generator_function()
    {
        $this->assertEquals(3, $this->collection->count());
        $this->assertEquals(1, $this->collection->get(0));
        $this->assertEquals(2, $this->collection->get(1));
        $this->assertEquals(3, $this->collection->get(2));
    }

    /** @test */
    public function can_filter_collection()
    {
        $filtered = $this->collection->filter(function ($value) {
            return $value > 1;
        });

        $this->assertEquals(2, $filtered->count());
        $this->assertEquals([2, 3], array_values($filtered->all()));
    }

    /** @test */
    public function can_get_all_items()
    {
        $this->assertEquals([1, 2, 3], $this->collection->all());
    }

    /** @test */
    public function can_get_first_item()
    {
        $this->assertEquals(1, $this->collection->first());
    }

    /** @test */
    public function can_get_first_item_with_callback()
    {
        $this->assertEquals(3, $this->collection->first(function ($value) {
            return $value === 3;
        }));
    }

    /** @test */
    public function can_map_collection()
    {
        $mapped = $this->collection->map(function ($value) {
            return $value * 2;
        });

        $this->assertEquals([2, 4, 6], $mapped->all());
    }

    /** @test */
    public function can_take_items()
    {
        $taken = $this->collection->take(2);

        $this->assertEquals(2, $taken->count());
        $this->assertEquals([1, 2], $taken->all());
    }

    /** @test */
    public function can_check_every_item()
    {
        $this->assertTrue($this->collection->every(function ($value) {
            return $value > 0;
        }));

        $this->assertFalse($this->collection->every(function ($value) {
            return $value > 1;
        }));
    }

    /** @test */
    public function can_chain_methods()
    {
        $result = $this->collection
            ->filter(function ($value) {
                return $value > 1;
            })
            ->map(function ($value) {
                return $value * 2;
            })
            ->take(1);

        $this->assertEquals(1, $result->count());
        $this->assertEquals(4, $result->first());
    }

    /** @test */
    public function can_iterate_over_collection()
    {
        $items = [];
        foreach ($this->collection as $item) {
            $items[] = $item;
        }

        $this->assertEquals([1, 2, 3], $items);
    }

    /** @test */
    public function can_get_item_by_key()
    {
        $this->assertEquals(2, $this->collection->get(1));
        $this->assertNull($this->collection->get(99));
    }
}
