<?php

declare(strict_types=1);

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Fake\SequenceMockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentsRequest;
use Mollie\Api\Resources\Payment;
use PHPUnit\Framework\TestCase;

class PaymentLazyIteratorTest extends TestCase
{
    /** @test */
    public function iterator_walks_across_pages_and_yields_typed_payments(): void
    {
        // First page via the initial request class; subsequent pages fetched
        // via DynamicGetRequest following the `_links.next.href` cursor.
        $client = new MockMollieClient([
            GetPaginatedPaymentsRequest::class => MockResponse::ok('cursor-collection-next', 'tr_page1'),
            DynamicGetRequest::class => new SequenceMockResponse(
                MockResponse::ok('cursor-collection-next', 'tr_page2'),
                MockResponse::ok('cursor-collection', 'tr_page3'),
            ),
        ]);

        $ids = [];
        foreach ($client->payments->iterator() as $payment) {
            $this->assertInstanceOf(Payment::class, $payment);
            $ids[] = $payment->id;
        }

        $this->assertSame(['tr_page1', 'tr_page2', 'tr_page3'], $ids);
    }
}
