<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetAllPaginatedSubscriptionsRequest;
use Mollie\Api\Resources\SubscriptionCollection;
use PHPUnit\Framework\TestCase;

class GetAllPaginatedSubscriptionsRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_subscriptions()
    {
        $client = new MockMollieClient([
            GetAllPaginatedSubscriptionsRequest::class => MockResponse::ok('subscription-list'),
        ]);

        $request = new GetAllPaginatedSubscriptionsRequest;

        /** @var SubscriptionCollection */
        $subscriptions = $client->send($request);

        $this->assertTrue($subscriptions->getResponse()->successful());
        $this->assertInstanceOf(SubscriptionCollection::class, $subscriptions);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetAllPaginatedSubscriptionsRequest;

        $this->assertEquals('subscriptions', $request->resolveResourcePath());
    }
}
