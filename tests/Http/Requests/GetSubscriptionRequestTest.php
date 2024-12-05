<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetSubscriptionRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Subscription;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetSubscriptionRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_subscription()
    {
        $client = new MockClient([
            GetSubscriptionRequest::class => new MockResponse(200, 'subscription'),
        ]);

        $request = new GetSubscriptionRequest('cst_kEn1PlbGa', 'sub_rVKGtNd6s3');

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());

        /** @var Subscription */
        $subscription = $response->toResource();

        $this->assertInstanceOf(Subscription::class, $subscription);
        $this->assertEquals('subscription', $subscription->resource);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetSubscriptionRequest('cst_kEn1PlbGa', 'sub_rVKGtNd6s3');

        $this->assertEquals(
            'customers/cst_kEn1PlbGa/subscriptions/sub_rVKGtNd6s3',
            $request->resolveResourcePath()
        );
    }
}
