<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetAllPaginatedSubscriptionsRequest;
use Mollie\Api\Resources\SubscriptionCollection;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class GetAllPaginatedSubscriptionsRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_subscriptions()
    {
        $client = new MockClient([
            GetAllPaginatedSubscriptionsRequest::class => new MockResponse(200, 'subscription-list'),
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
