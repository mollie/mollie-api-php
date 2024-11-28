<?php

namespace Tests\EndpointCollection;

use Mollie\Api\EndpointCollection\SettlementsEndpointCollection;
use Mollie\Api\Http\Requests\GetPaginatedSettlementsRequest;
use Mollie\Api\Http\Requests\GetSettlementRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Settlement;
use Mollie\Api\Resources\SettlementCollection;
use PHPUnit\Framework\TestCase;

class SettlementsEndpointCollectionTest extends TestCase
{
    private SettlementsEndpointCollection $collection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->collection = $this->getMockBuilder(SettlementsEndpointCollection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['send'])
            ->getMock();
    }

    public function testGet(): void
    {
        $settlement = $this->createMock(Settlement::class);

        $this->collection
            ->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(GetSettlementRequest::class))
            ->willReturn($settlement);

        $result = $this->collection->get('stl_123');

        $this->assertSame($settlement, $result);
    }

    public function testNext(): void
    {
        $settlement = $this->createMock(Settlement::class);

        $this->collection
            ->expects($this->once())
            ->method('send')
            ->with($this->callback(function (GetSettlementRequest $request) {
                return $request->getId() === 'next';
            }))
            ->willReturn($settlement);

        $result = $this->collection->next();

        $this->assertSame($settlement, $result);
    }

    public function testOpen(): void
    {
        $settlement = $this->createMock(Settlement::class);

        $this->collection
            ->expects($this->once())
            ->method('send')
            ->with($this->callback(function (GetSettlementRequest $request) {
                return $request->getId() === 'open';
            }))
            ->willReturn($settlement);

        $result = $this->collection->open();

        $this->assertSame($settlement, $result);
    }

    public function testPage(): void
    {
        $collection = $this->createMock(SettlementCollection::class);

        $this->collection
            ->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(GetPaginatedSettlementsRequest::class))
            ->willReturn($collection);

        $result = $this->collection->page('stl_123', 50, ['reference' => 'test']);

        $this->assertSame($collection, $result);
    }

    public function testIterator(): void
    {
        $lazyCollection = $this->createMock(LazyCollection::class);

        $this->collection
            ->expects($this->once())
            ->method('send')
            ->with($this->callback(function (GetPaginatedSettlementsRequest $request) {
                return $request->isIterator() && !$request->isIteratingBackwards();
            }))
            ->willReturn($lazyCollection);

        $result = $this->collection->iterator('stl_123', 50, ['reference' => 'test']);

        $this->assertSame($lazyCollection, $result);
    }

    public function testIteratorWithBackwardsIteration(): void
    {
        $lazyCollection = $this->createMock(LazyCollection::class);

        $this->collection
            ->expects($this->once())
            ->method('send')
            ->with($this->callback(function (GetPaginatedSettlementsRequest $request) {
                return $request->isIterator() && $request->isIteratingBackwards();
            }))
            ->willReturn($lazyCollection);

        $result = $this->collection->iterator('stl_123', 50, ['reference' => 'test'], true);

        $this->assertSame($lazyCollection, $result);
    }
}
