<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedSubscriptionsRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Subscription;
use Mollie\Api\Resources\SubscriptionCollection;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\Fixtures\SequenceMockResponse;

class GetPaginatedSubscriptionsRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_subscriptions()
    {
        $client = new MockClient([
            GetPaginatedSubscriptionsRequest::class => new MockResponse(200, 'subscription-list'),
        ]);

        $request = new GetPaginatedSubscriptionsRequest('cst_kEn1PlbGa');

        /** @var SubscriptionCollection */
        $subscriptions = $client->send($request);

        $this->assertTrue($subscriptions->getResponse()->successful());
        $this->assertInstanceOf(SubscriptionCollection::class, $subscriptions);
        $this->assertGreaterThan(0, $subscriptions->count());

        foreach ($subscriptions as $subscription) {
            $this->assertInstanceOf(Subscription::class, $subscription);
            $this->assertEquals('subscription', $subscription->resource);
        }
    }

    /** @test */
    public function it_can_iterate_over_subscriptions()
    {
        $client = new MockClient([
            GetPaginatedSubscriptionsRequest::class => new MockResponse(200, 'subscription-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                new MockResponse(200, 'subscription-list'),
                new MockResponse(200, 'empty-list', 'subscriptions'),
            ),
        ]);

        $request = (new GetPaginatedSubscriptionsRequest('cst_kEn1PlbGa'))->useIterator();

        /** @var LazyCollection */
        $subscriptions = $client->send($request);
        $this->assertTrue($subscriptions->getResponse()->successful());

        foreach ($subscriptions as $subscription) {
            $this->assertInstanceOf(Subscription::class, $subscription);
        }

        $client->assertSentCount(3);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $customerId = 'cst_kEn1PlbGa';
        $request = new GetPaginatedSubscriptionsRequest($customerId);

        $this->assertEquals("customers/{$customerId}/subscriptions", $request->resolveResourcePath());
    }
}
