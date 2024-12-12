<?php

namespace Tests\EndpointCollection;

use DateTimeImmutable;
use Mollie\Api\Http\Payload\CreatePaymentLinkPayload;
use Mollie\Api\Http\Payload\Money;
use Mollie\Api\Http\Payload\UpdatePaymentLinkPayload;
use Mollie\Api\Http\Requests\CreatePaymentLinkRequest;
use Mollie\Api\Http\Requests\DeletePaymentLinkRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentLinksRequest;
use Mollie\Api\Http\Requests\GetPaymentLinkRequest;
use Mollie\Api\Http\Requests\UpdatePaymentLinkRequest;
use Mollie\Api\Resources\PaymentLink;
use Mollie\Api\Resources\PaymentLinkCollection;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class PaymentLinkEndpointCollectionTest extends TestCase
{
    /** @test */
    public function create()
    {
        $client = new MockClient([
            CreatePaymentLinkRequest::class => new MockResponse(201, 'payment-link'),
        ]);

        /** @var PaymentLink $paymentLink */
        $paymentLink = $client->paymentLinks->create(new CreatePaymentLinkPayload(
            'Test payment link',
            new Money('10.00', 'EUR'),
            'https://example.org/redirect',
            'https://example.org/webhook',
            null,
            null,
            new DateTimeImmutable('2023-12-31'),
        ));

        $this->assertPaymentLink($paymentLink);
    }

    /** @test */
    public function get()
    {
        $client = new MockClient([
            GetPaymentLinkRequest::class => new MockResponse(200, 'payment-link'),
        ]);

        /** @var PaymentLink $paymentLink */
        $paymentLink = $client->paymentLinks->get('pl_4Y0eZitmBnQ6IDoMqZQKh');

        $this->assertPaymentLink($paymentLink);
    }

    /** @test */
    public function update()
    {
        $client = new MockClient([
            UpdatePaymentLinkRequest::class => new MockResponse(200, 'payment-link'),
        ]);

        /** @var PaymentLink $paymentLink */
        $paymentLink = $client->paymentLinks->update('pl_4Y0eZitmBnQ6IDoMqZQKh', new UpdatePaymentLinkPayload(
            'Updated description',
        ));

        $this->assertPaymentLink($paymentLink);
    }

    /** @test */
    public function delete()
    {
        $client = new MockClient([
            DeletePaymentLinkRequest::class => new MockResponse(204),
        ]);

        $client->paymentLinks->delete('pl_4Y0eZitmBnQ6IDoMqZQKh');

        // Test passes if no exception is thrown
        $this->assertTrue(true);
    }

    /** @test */
    public function page()
    {
        $client = new MockClient([
            GetPaginatedPaymentLinksRequest::class => new MockResponse(200, 'payment-link-list'),
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
        $client = new MockClient([
            GetPaginatedPaymentLinksRequest::class => new MockResponse(200, 'payment-link-list'),
            DynamicGetRequest::class => new MockResponse(200, 'empty-list', 'payment_links'),
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