<?php

declare(strict_types=1);

namespace Tests\Resources;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Fake\SequenceMockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\PaymentCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

class CursorCollectionTest extends TestCase
{
    private Response $response;

    protected function setUp(): void
    {
        parent::setUp();

        $this->response = $this->createMock(Response::class);
    }

    #[Test]
    public function can_get_next_collection_result_when_next_link_is_available()
    {
        $client = new MockMollieClient([
            DynamicGetRequest::class => MockResponse::ok('cursor-collection'),
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

        $collection->setResponse($this->response);

        $this->assertTrue($collection->hasNext());

        $nextPage = $collection->next();

        $this->assertFalse($nextPage->hasNext());
    }

    #[Test]
    public function will_return_null_if_no_next_result_is_available()
    {
        $client = new MockMollieClient;

        $collection = new PaymentCollection(
            $client,
            [],
            (object) []
        );

        $collection->setResponse($this->response);

        $this->assertFalse($collection->hasNext());
        $this->assertNull($collection->next());
    }

    #[Test]
    public function can_get_previous_collection_result_when_previous_link_is_available()
    {
        $client = new MockMollieClient([
            DynamicGetRequest::class => MockResponse::ok('cursor-collection'),
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

        $collection->setResponse($this->response);

        $this->assertTrue($collection->hasPrevious());

        $previousPage = $collection->previous();

        $this->assertFalse($previousPage->hasPrevious());
    }

    #[Test]
    public function will_return_null_if_no_previous_result_is_available()
    {
        $client = new MockMollieClient;

        $collection = new PaymentCollection(
            $client,
            [],
            (object) []
        );

        $collection->setResponse($this->response);

        $this->assertFalse($collection->hasPrevious());
        $this->assertNull($collection->previous());
    }

    #[Test]
    public function auto_paginator_returns_lazy_collection()
    {
        $client = new MockMollieClient;

        $collection = new PaymentCollection(
            $client,
            [],
            (object) []
        );

        $collection->setResponse($this->response);

        $this->assertInstanceOf(LazyCollection::class, $collection->getAutoIterator());
    }

    #[Test]
    public function auto_paginator_can_handle_consecutive_calls()
    {
        $client = new MockMollieClient([
            DynamicGetRequest::class => new SequenceMockResponse(
                MockResponse::ok('cursor-collection-next', 'tr_stTC2WHAuF'),
                MockResponse::ok('cursor-collection-next', 'tr_stTC2WHAuS'),
                MockResponse::ok('cursor-collection', 'tr_stTC2WHAuB')
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

        $collection->setResponse($this->response);

        $paymentIds = [];
        foreach ($collection->getAutoIterator() as $payment) {
            $paymentIds[] = $payment->id;
        }

        $this->assertEquals(['tr_stTC2WHAuF', 'tr_stTC2WHAuS', 'tr_stTC2WHAuB'], $paymentIds);
    }

    #[Test]
    public function backwards_auto_paginator_walks_from_last_page_to_first_page(): void
    {
        $client = new MockMollieClient([
            DynamicGetRequest::class => new SequenceMockResponse(
                MockResponse::ok($this->cursorPage('tr_middle', 'tr_first', 'tr_last')),
                MockResponse::ok($this->cursorPage('tr_first', null, 'tr_middle')),
            ),
        ]);

        $collection = $this->paymentCollection($client, 'tr_last', 'tr_middle');

        $this->assertSame(
            ['tr_last', 'tr_middle', 'tr_first'],
            $this->paymentIds($collection->getAutoIterator(true)),
        );
    }

    #[Test]
    public function backwards_auto_paginator_walks_from_middle_page_to_first_page(): void
    {
        $client = new MockMollieClient([
            DynamicGetRequest::class => MockResponse::ok($this->cursorPage('tr_first', null, 'tr_middle')),
        ]);

        $collection = $this->paymentCollection($client, 'tr_middle', 'tr_first', 'tr_last');

        $this->assertSame(
            ['tr_middle', 'tr_first'],
            $this->paymentIds($collection->getAutoIterator(true)),
        );
    }

    #[Test]
    public function backwards_auto_paginator_stops_on_first_page(): void
    {
        $collection = $this->paymentCollection(new MockMollieClient, 'tr_first', null, 'tr_middle');

        $this->assertSame(
            ['tr_first'],
            $this->paymentIds($collection->getAutoIterator(true)),
        );
    }

    private function paymentCollection(
        MockMollieClient $client,
        string $id,
        ?string $previous = null,
        ?string $next = null,
    ): PaymentCollection {
        $collection = new PaymentCollection(
            $client,
            [(object) ['id' => $id]],
            $this->arrayToObject($this->cursorLinks($previous, $next)),
        );

        return $collection->setResponse($this->response);
    }

    private function paymentIds(LazyCollection $payments): array
    {
        $ids = [];

        foreach ($payments as $payment) {
            $ids[] = $payment->id;
        }

        return $ids;
    }

    private function cursorPage(string $id, ?string $previous, ?string $next): array
    {
        return [
            '_links' => $this->cursorLinks($previous, $next),
            '_embedded' => [
                'payments' => [
                    ['id' => $id],
                ],
            ],
        ];
    }

    private function cursorLinks(?string $previous, ?string $next): array
    {
        $links = [];

        if ($previous !== null) {
            $links['previous']['href'] = 'https://api.mollie.com/v2/payments?from='.$previous;
        }

        if ($next !== null) {
            $links['next']['href'] = 'https://api.mollie.com/v2/payments?from='.$next;
        }

        return $links;
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
