<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetAllPaginatedSubscriptionsRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\SubscriptionCollection;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetAllPaginatedSubscriptionsRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_subscriptions()
    {
        $client = new MockClient([
            GetAllPaginatedSubscriptionsRequest::class => new MockResponse(200, 'subscription-list'),
        ]);

        $request = new GetAllPaginatedSubscriptionsRequest;

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(SubscriptionCollection::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetAllPaginatedSubscriptionsRequest;

        $this->assertEquals('subscriptions', $request->resolveResourcePath());
    }
}
