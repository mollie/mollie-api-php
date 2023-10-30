<?php

namespace Tests\Mollie\API\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\OrderCollection;
use PHPUnit\Framework\TestCase;

class CursorCollectionTest extends TestCase
{
    public function testCanGetNextCollectionResultWhenNextLinkIsAvailable()
    {
        $mockedClient = $this->createMock(MollieApiClient::class);
        $mockedClient->expects($this->once())
            ->method('performHttpCallToFullUrl')
            ->willReturn((object) [
                'count' => 1,
                '_links' => (object) [
                    'self' => [
                        'href' => 'https://api.mollie.com/v2/orders?from=ord_stTC2WHAuS',
                    ],
                ],
                '_embedded' => (object) [
                    'orders' => [
                        (object) ['id' => 'ord_stTC2WHAuS'],
                    ],
                ],
            ]);

        $collection = new OrderCollection(
            $mockedClient,
            1,
            (object) [
                'next' => (object) [
                    'href' => 'https://api.mollie.com/v2/orders?from=ord_stTC2WHAuS',
                ],
            ]
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
            ->willReturn((object) [
                'count' => 1,
                '_links' => (object) [
                    'self' => [
                        'href' => 'https://api.mollie.com/v2/orders?from=ord_stTC2WHAuS',
                    ],
                ],
                '_embedded' => (object) [
                    'orders' => [
                        (object) ['id' => 'ord_stTC2WHAuS'],
                    ],
                ],
            ]);

        $collection = new OrderCollection(
            $mockedClient,
            1,
            (object) [
                'previous' => (object) [
                    'href' => 'https://api.mollie.com/v2/orders?from=ord_stTC2WHAuS',
                ],
            ]
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
}
