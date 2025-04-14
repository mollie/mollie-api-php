<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\UpdateSubscriptionRequest;
use Mollie\Api\Resources\Subscription;
use PHPUnit\Framework\TestCase;

class UpdateSubscriptionRequestTest extends TestCase
{
    /** @test */
    public function it_can_update_subscription()
    {
        $client = new MockMollieClient([
            UpdateSubscriptionRequest::class => MockResponse::ok('subscription'),
        ]);

        $customerId = 'cst_kEn1PlbGa';
        $subscriptionId = 'sub_rVKGtNd6s3';

        $request = new UpdateSubscriptionRequest(
            $customerId,
            $subscriptionId,
            new Money('EUR', '20.00'),
            'Updated subscription',
            '1 month'
        );

        $this->assertEquals(
            "customers/{$customerId}/subscriptions/{$subscriptionId}",
            $request->resolveResourcePath()
        );

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
        $request = new UpdateSubscriptionRequest(
            $customerId,
            $subscriptionId,
            new Money('EUR', '20.00'),
            'Updated subscription',
            '1 month'
        );

        $this->assertEquals("customers/{$customerId}/subscriptions/{$subscriptionId}", $request->resolveResourcePath());
    }
}
