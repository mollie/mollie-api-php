<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetSubscriptionRequest;
use Mollie\Api\Resources\Subscription;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class GetSubscriptionRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_subscription()
    {
        $client = new MockClient([
            GetSubscriptionRequest::class => new MockResponse(200, 'subscription'),
        ]);

        $request = new GetSubscriptionRequest('cst_kEn1PlbGa', 'sub_rVKGtNd6s3');

        /** @var Subscription */
        $subscription = $client->send($request);

        $this->assertTrue($subscription->getResponse()->successful());
        $this->assertInstanceOf(Subscription::class, $subscription);
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
