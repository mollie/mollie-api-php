<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\CancelSubscriptionRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Subscription;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class CancelSubscriptionRequestTest extends TestCase
{
    /** @test */
    public function it_can_cancel_subscription()
    {
        $client = new MockClient([
            CancelSubscriptionRequest::class => new MockResponse(200, 'subscription'),
        ]);

        $customerId = 'cst_kEn1PlbGa';
        $subscriptionId = 'sub_rVKGtNd6s3';
        $request = new CancelSubscriptionRequest($customerId, $subscriptionId);

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(Subscription::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $customerId = 'cst_kEn1PlbGa';
        $subscriptionId = 'sub_rVKGtNd6s3';
        $request = new CancelSubscriptionRequest($customerId, $subscriptionId);

        $this->assertEquals(
            "customers/{$customerId}/subscriptions/{$subscriptionId}",
            $request->resolveResourcePath()
        );
    }
}
