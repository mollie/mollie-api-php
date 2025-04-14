<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\CancelPaymentRefundRequest;
use Mollie\Api\Http\Requests\CreatePaymentRefundRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentRefundsRequest;
use Mollie\Api\Http\Requests\GetPaymentRefundRequest;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\RefundCollection;
use PHPUnit\Framework\TestCase;

class PaymentRefundEndpointCollectionTest extends TestCase
{
    /** @test */
    public function create_for()
    {
        $client = new MockMollieClient([
            CreatePaymentRefundRequest::class => MockResponse::created('refund'),
        ]);

        $payment = new Payment($client);
        $payment->id = 'tr_7UhSN1zuXS';

        /** @var Refund $refund */
        $refund = $client->paymentRefunds->createFor($payment, [
            'amount' => [
                'currency' => 'EUR',
                'value' => '5.95',
            ],
            'description' => 'Test refund',
        ]);

        $this->assertRefund($refund);
    }

    /** @test */
    public function get_for()
    {
        $client = new MockMollieClient([
            GetPaymentRefundRequest::class => MockResponse::ok('refund'),
        ]);

        $payment = new Payment($client);
        $payment->id = 'tr_7UhSN1zuXS';

        /** @var Refund $refund */
        $refund = $client->paymentRefunds->getFor($payment, 're_4qqhO89gsT');

        $this->assertRefund($refund);
    }

    /** @test */
    public function cancel_for()
    {
        $client = new MockMollieClient([
            CancelPaymentRefundRequest::class => MockResponse::noContent(),
        ]);

        $payment = new Payment($client);
        $payment->id = 'tr_7UhSN1zuXS';

        $client->paymentRefunds->cancelForPayment($payment, 're_4qqhO89gsT');

        // Test passes if no exception is thrown
        $this->assertTrue(true);
    }

    /** @test */
    public function page_for()
    {
        $client = new MockMollieClient([
            GetPaginatedPaymentRefundsRequest::class => MockResponse::ok('refund-list'),
        ]);

        $payment = new Payment($client);
        $payment->id = 'tr_7UhSN1zuXS';

        /** @var RefundCollection $refunds */
        $refunds = $client->paymentRefunds->pageFor($payment);

        $this->assertInstanceOf(RefundCollection::class, $refunds);
        $this->assertGreaterThan(0, $refunds->count());
        $this->assertGreaterThan(0, count($refunds));

        foreach ($refunds as $refund) {
            $this->assertRefund($refund);
        }
    }

    /** @test */
    public function iterator_for()
    {
        $client = new MockMollieClient([
            GetPaginatedPaymentRefundsRequest::class => MockResponse::ok('refund-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'refunds'),
        ]);

        $payment = new Payment($client);
        $payment->id = 'tr_7UhSN1zuXS';

        foreach ($client->paymentRefunds->iteratorFor($payment) as $refund) {
            $this->assertInstanceOf(Refund::class, $refund);
            $this->assertRefund($refund);
        }
    }

    protected function assertRefund(Refund $refund)
    {
        $this->assertInstanceOf(Refund::class, $refund);
        $this->assertEquals('refund', $refund->resource);
        $this->assertNotEmpty($refund->id);
        $this->assertNotEmpty($refund->amount);
        $this->assertNotEmpty($refund->status);
        $this->assertEquals('tr_7UhSN1zuXS', $refund->paymentId);
        $this->assertNotEmpty($refund->createdAt);
        $this->assertNotEmpty($refund->_links);
    }
}
