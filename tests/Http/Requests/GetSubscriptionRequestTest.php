<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetSubscriptionRequest;
use Mollie\Api\Resources\Subscription;
use PHPUnit\Framework\TestCase;

class GetSubscriptionRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_subscription()
    {
        $client = new MockMollieClient([
            GetSubscriptionRequest::class => MockResponse::ok('subscription'),
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
