<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\CancelSubscriptionRequest;
use Mollie\Api\Resources\Subscription;
use PHPUnit\Framework\TestCase;

class CancelSubscriptionRequestTest extends TestCase
{
    /** @test */
    public function it_can_cancel_subscription()
    {
        $client = new MockMollieClient([
            CancelSubscriptionRequest::class => MockResponse::ok('subscription'),
        ]);

        $customerId = 'cst_kEn1PlbGa';
        $subscriptionId = 'sub_rVKGtNd6s3';
        $request = new CancelSubscriptionRequest($customerId, $subscriptionId);

        /** @var Subscription */
        $subscription = $client->send($request);

        $this->assertTrue($subscription->getResponse()->successful());
        $this->assertInstanceOf(Subscription::class, $subscription);
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
