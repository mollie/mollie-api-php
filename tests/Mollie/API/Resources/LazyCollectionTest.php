<?php

namespace Tests\Mollie\API\Resources;

use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\MandateCollection;
use Mollie\Api\Types\MandateStatus;
use PHPUnit\Framework\TestCase;

class LazyCollectionTest extends TestCase
{
    private LazyCollection $collection;

    public function setUp(): void
    {
        parent::setUp();

        $this->collection = new LazyCollection(function () {
            yield 1;
            yield 2;
            yield 3;
        });
    }

    public function testCanCreateACollectionFromGeneratorFunction()
    {
        $this->assertEquals(3, $this->collection->count());
        $this->assertEquals(1, $this->collection->get(0));
        $this->assertEquals(2, $this->collection->get(1));
        $this->assertEquals(3, $this->collection->get(2));
    }

    public function testFilter()
    {
        $filtered = $this->collection->filter(function ($value) {
            return $value > 1;
        });

        $this->assertEquals(2, $filtered->count());
    }

    public function testAll()
    {
        $this->assertEquals([1, 2, 3], $this->collection->all());
    }

    public function testFirst()
    {
        $this->assertEquals(1, $this->collection->first());
        $this->assertEquals(3, $this->collection->first(function ($value) {
            return $value === 3;
        }));
    }

    public function testMap()
    {
        $mapped = $this->collection->map(function ($value) {
            return $value * 2;
        });


        $mapped->every(function ($value, $key) {
            $this->assertEquals($value, $this->collection->get($key) * 2);

            return true;
        });
    }

    public function testTake()
    {
        $taken = $this->collection->take(2);

        $this->assertEquals(2, $taken->count());
    }

    public function testEvery()
    {
        $this->assertTrue($this->collection->every(function ($value) {
            return $value > 0;
        }));

        $this->assertFalse($this->collection->every(function ($value) {
            return $value > 1;
        }));
    }

    public function testItRemainsAccessToOriginalCollectionMethods()
    {
        $collection = new MandateCollection(
            $this->createMock(\Mollie\Api\MollieApiClient::class),
            1,
            (object) []
        );
        $collection[0] = (object) ['id' => 'mdt_12345', 'status' => MandateStatus::STATUS_VALID];
        $collection[1] = (object) ['id' => 'mdt_12346', 'status' => MandateStatus::STATUS_PENDING];

        $pendingMandates = $collection->getAutoIterator()->whereStatus(MandateStatus::STATUS_PENDING);
        $this->assertCount(1, $pendingMandates);
    }
}
