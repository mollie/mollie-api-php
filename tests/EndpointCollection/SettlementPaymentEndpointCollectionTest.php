<?php

declare(strict_types=1);

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedSettlementPaymentsRequest;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Resources\Settlement;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SettlementPaymentEndpointCollectionTest extends TestCase
{
    #[Test]
    public function page_for()
    {
        $client = new MockMollieClient([
            GetPaginatedSettlementPaymentsRequest::class => MockResponse::ok('payment-list'),
        ]);

        $settlement = new Settlement($client);
        $settlement->id = 'stl_jDk30akdN';

        /** @var PaymentCollection $payments */
        $payments = $client->settlementPayments->pageFor($settlement, [
            'from' => 'tr_from_page',
            'limit' => 25,
            'filters' => ['sort' => 'desc'],
            'testmode' => true,
        ]);

        $this->assertInstanceOf(PaymentCollection::class, $payments);
        $this->assertGreaterThan(0, $payments->count());

        $client->assertSent(function (PendingRequest $pendingRequest) {
            $request = $pendingRequest->getRequest();

            $this->assertInstanceOf(GetPaginatedSettlementPaymentsRequest::class, $request);
            $this->assertSame([
                'from' => 'tr_from_page',
                'limit' => 25,
                'sort' => 'desc',
                'testmode' => 'true',
            ], $pendingRequest->query()->all());
            $this->assertFalse($request->iteratorEnabled());
            $this->assertFalse($request->iteratesBackwards());
            $this->assertTrue($request->getTestmode());

            return true;
        });

        foreach ($payments as $payment) {
            $this->assertPayment($payment);
        }
    }

    #[Test]
    public function iterator_for()
    {
        $client = new MockMollieClient([
            GetPaginatedSettlementPaymentsRequest::class => MockResponse::ok('payment-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'payments'),
        ]);

        $settlement = new Settlement($client);
        $settlement->id = 'stl_jDk30akdN';

        foreach ($client->settlementPayments->iteratorFor(
            $settlement,
            'tr_from_iterator',
            50,
            ['sort' => 'asc', 'testmode' => true],
            true
        ) as $payment) {
            $this->assertPayment($payment);
        }

        $client->assertSent(function (PendingRequest $pendingRequest) {
            $request = $pendingRequest->getRequest();

            if (! $request instanceof GetPaginatedSettlementPaymentsRequest) {
                return false;
            }

            $this->assertSame([
                'from' => 'tr_from_iterator',
                'limit' => 50,
                'sort' => 'asc',
                'testmode' => 'true',
            ], $pendingRequest->query()->all());
            $this->assertTrue($request->iteratorEnabled());
            $this->assertTrue($request->iteratesBackwards());
            $this->assertTrue($request->getTestmode());

            return true;
        });
    }

    protected function assertPayment(Payment $payment)
    {
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals('payment', $payment->resource);
        $this->assertNotEmpty($payment->id);
        $this->assertNotEmpty($payment->amount);
        $this->assertNotEmpty($payment->description);
        $this->assertNotEmpty($payment->createdAt);
        $this->assertNotEmpty($payment->status);
        $this->assertNotEmpty($payment->profileId);
        $this->assertNotEmpty($payment->_links);
    }
}
