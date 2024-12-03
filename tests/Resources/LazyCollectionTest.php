<?php

namespace Tests\Resources;

use Mollie\Api\Resources\LazyCollection;
use Tests\TestCase;

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
    }

    public function test_can_create_a_collection_from_generator_function()
    {
        $this->assertEquals(3, $this->collection->count());
        $this->assertEquals(1, $this->collection->get(0));
        $this->assertEquals(2, $this->collection->get(1));
        $this->assertEquals(3, $this->collection->get(2));
    }

    public function test_filter()
    {
        $filtered = $this->collection->filter(function ($value) {
            return $value > 1;
        });

        $this->assertEquals(2, $filtered->count());
    }

    public function test_all()
    {
        $this->assertEquals([1, 2, 3], $this->collection->all());
    }

    public function test_first()
    {
        $this->assertEquals(1, $this->collection->first());
        $this->assertEquals(3, $this->collection->first(function ($value) {
            return $value === 3;
        }));
    }

    public function test_map()
    {
        $mapped = $this->collection->map(function ($value) {
            return $value * 2;
        });

        $mapped->every(function ($value, $key) {
            $this->assertEquals($value, $this->collection->get($key) * 2);

            return true;
        });
    }

    public function test_take()
    {
        $taken = $this->collection->take(2);

        $this->assertEquals(2, $taken->count());
    }

    public function test_every()
    {
        $this->assertTrue($this->collection->every(function ($value) {
            return $value > 0;
        }));

        $this->assertFalse($this->collection->every(function ($value) {
            return $value > 1;
        }));
    }

    public function test_chained_usage()
    {
        $result = $this->collection
            ->filter(function ($value) {
                return $value > 1;
            })->map(function ($value) {
                return $value * 2;
            })->take(1);

        $this->assertEquals(1, $result->count());
        $this->assertEquals(4, $result->first());
    }
}
