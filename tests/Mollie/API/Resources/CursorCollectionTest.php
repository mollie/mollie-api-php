<?php

namespace Tests\Mollie\API\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\OrderCollection;
use PHPUnit\Framework\TestCase;
use stdClass;

class CursorCollectionTest extends TestCase
{
    public function testCanGetNextCollectionResultWhenNextLinkIsAvailable()
    {
        $mockedClient = $this->createMock(MollieApiClient::class);
        $mockedClient->expects($this->once())
            ->method('performHttpCallToFullUrl')
            ->willReturn($this->arrayToObject([
                'count' => 1,
                '_links' => [
                    'self' => [
                        'href' => 'https://api.mollie.com/v2/orders?from=ord_stTC2WHAuS',
                    ],
                ],
                '_embedded' => [
                    'orders' => [
                        ['id' => 'ord_stTC2WHAuS'],
                    ],
                ],
            ]));

        $collection = new OrderCollection(
            $mockedClient,
            1,
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
        $mockedClient = $this->createMock(MollieApiClient::class);
        $collection = new OrderCollection(
            $mockedClient,
            1,
            (object) []
        );

        $this->assertFalse($collection->hasNext());
        $this->assertNull($collection->next());
    }

    public function testCanGetPreviousCollectionResultWhenPreviousLinkIsAvailable()
    {
        $mockedClient = $this->createMock(MollieApiClient::class);
        $mockedClient->expects($this->once())
            ->method('performHttpCallToFullUrl')
            ->willReturn(
                $this->arrayToObject([
                    'count' => 1,
                    '_links' => [
                        'self' => [
                            'href' => 'https://api.mollie.com/v2/orders?from=ord_stTC2WHAuS',
                        ],
                    ],
                    '_embedded' => [
                        'orders' => [
                            ['id' => 'ord_stTC2WHAuS'],
                        ],
                    ],
                ])
            );

        $collection = new OrderCollection(
            $mockedClient,
            1,
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
        $mockedClient = $this->createMock(MollieApiClient::class);
        $collection = new OrderCollection(
            $mockedClient,
            1,
            (object) []
        );

        $this->assertFalse($collection->hasPrevious());
        $this->assertNull($collection->previous());
    }

    public function testAutoPaginatorReturnsLazyCollection()
    {
        $collection = new OrderCollection(
            $this->createMock(MollieApiClient::class),
            1,
            (object) []
        );

        $this->assertInstanceOf(LazyCollection::class, $collection->getAutoIterator());
    }

    public function testAutoPaginatorCanHandleConsecutiveCalls()
    {
        $mockedClient = $this->createMock(MollieApiClient::class);
        $mockedClient->expects($this->exactly(3))
            ->method('performHttpCallToFullUrl')
            ->willReturnOnConsecutiveCalls(
                $this->arrayToObject([
                    'count' => 1,
                    '_links' => [
                        'self' => [
                            'href' => 'https://api.mollie.com/v2/orders?from=ord_stTC2WHAuS',
                        ],
                        'next' => [
                            'href' => 'https://api.mollie.com/v2/orders?from=ord_stTC2WHAuS',
                        ],
                    ],
                    '_embedded' => [
                        'orders' => [
                            ['id' => 'ord_stTC2WHAuS'],
                        ],
                    ],
                ]),
                $this->arrayToObject([
                    'count' => 1,
                    '_links' => [
                        'self' => [
                            'href' => 'https://api.mollie.com/v2/orders?from=ord_stTC2WHAuF',
                        ],
                        'next' => [
                            'href' => 'https://api.mollie.com/v2/orders?from=ord_stTC2WHAuF',
                        ],
                    ],
                    '_embedded' => [
                        'orders' => [
                            ['id' => 'ord_stTC2WHAuF'],
                        ],
                    ],
                ]),
                $this->arrayToObject([
                    'count' => 1,
                    '_links' => [
                        'self' => [
                            'href' => 'https://api.mollie.com/v2/orders?from=ord_stTC2WHAuB',
                        ],
                    ],
                    '_embedded' => [
                        'orders' => [
                            ['id' => 'ord_stTC2WHAuB'],
                        ],
                    ],
                ])
            );

        $collection = new OrderCollection(
            $mockedClient,
            0,
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

        $this->assertEquals(['ord_stTC2WHAuS', 'ord_stTC2WHAuF', 'ord_stTC2WHAuB'], $orderIds);
    }

    /**
     * Convert an array to an object recursively.
     *
     * @param mixed $data
     * @return mixed
     */
    private function arrayToObject($data)
    {
        if (! is_array($data)) {
            return $data;
        }

        $obj = new stdClass();

        foreach ($data as $key => $value) {
            $obj->$key = $this->arrayToObject($value);
        }

        return $obj;
    }
}
