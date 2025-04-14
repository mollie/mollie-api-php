<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreatePaymentLinkRequest;
use Mollie\Api\Http\Requests\DeletePaymentLinkRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentLinksRequest;
use Mollie\Api\Http\Requests\GetPaymentLinkRequest;
use Mollie\Api\Http\Requests\UpdatePaymentLinkRequest;
use Mollie\Api\Resources\PaymentLink;
use Mollie\Api\Resources\PaymentLinkCollection;
use PHPUnit\Framework\TestCase;

class PaymentLinkEndpointCollectionTest extends TestCase
{
    /** @test */
    public function create()
    {
        $client = new MockMollieClient([
            CreatePaymentLinkRequest::class => MockResponse::created('payment-link'),
        ]);

        /** @var PaymentLink $paymentLink */
        $paymentLink = $client->paymentLinks->create([
            'description' => 'Test payment link',
            'amount' => new Money('10.00', 'EUR'),
            'redirectUrl' => 'https://example.org/redirect',
            'webhookUrl' => 'https://example.org/webhook',
        ]);

        $this->assertPaymentLink($paymentLink);
    }

    /** @test */
    public function get()
    {
        $client = new MockMollieClient([
            GetPaymentLinkRequest::class => MockResponse::ok('payment-link'),
        ]);

        /** @var PaymentLink $paymentLink */
        $paymentLink = $client->paymentLinks->get('pl_4Y0eZitmBnQ6IDoMqZQKh');

        $this->assertPaymentLink($paymentLink);
    }

    /** @test */
    public function update()
    {
        $client = new MockMollieClient([
            UpdatePaymentLinkRequest::class => MockResponse::ok('payment-link'),
        ]);

        /** @var PaymentLink $paymentLink */
        $paymentLink = $client->paymentLinks->update('pl_4Y0eZitmBnQ6IDoMqZQKh', [
            'description' => 'Updated description',
        ]);

        $this->assertPaymentLink($paymentLink);
    }

    /** @test */
    public function delete()
    {
        $client = new MockMollieClient([
            DeletePaymentLinkRequest::class => MockResponse::noContent(),
        ]);

        $client->paymentLinks->delete('pl_4Y0eZitmBnQ6IDoMqZQKh');

        // Test passes if no exception is thrown
        $this->assertTrue(true);
    }

    /** @test */
    public function page()
    {
        $client = new MockMollieClient([
            GetPaginatedPaymentLinksRequest::class => MockResponse::ok('payment-link-list'),
        ]);

        /** @var PaymentLinkCollection $paymentLinks */
        $paymentLinks = $client->paymentLinks->page();

        $this->assertInstanceOf(PaymentLinkCollection::class, $paymentLinks);
        $this->assertGreaterThan(0, $paymentLinks->count());
        $this->assertGreaterThan(0, count($paymentLinks));

        foreach ($paymentLinks as $paymentLink) {
            $this->assertPaymentLink($paymentLink);
        }
    }

    /** @test */
    public function iterator()
    {
        $client = new MockMollieClient([
            GetPaginatedPaymentLinksRequest::class => MockResponse::ok('payment-link-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'payment_links'),
        ]);

        foreach ($client->paymentLinks->iterator() as $paymentLink) {
            $this->assertInstanceOf(PaymentLink::class, $paymentLink);
            $this->assertPaymentLink($paymentLink);
        }
    }

    protected function assertPaymentLink(PaymentLink $paymentLink)
    {
        $this->assertInstanceOf(PaymentLink::class, $paymentLink);
        $this->assertEquals('payment-link', $paymentLink->resource);
        $this->assertNotEmpty($paymentLink->id);
        $this->assertNotEmpty($paymentLink->description);
        $this->assertNotEmpty($paymentLink->amount);
        $this->assertNotEmpty($paymentLink->expiresAt);
        $this->assertNotEmpty($paymentLink->_links);
    }
}
