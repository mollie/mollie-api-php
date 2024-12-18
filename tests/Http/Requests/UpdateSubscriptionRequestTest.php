<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Data\UpdateSubscriptionPayload;
use Mollie\Api\Http\Requests\UpdateSubscriptionRequest;
use Mollie\Api\Resources\Subscription;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class UpdateSubscriptionRequestTest extends TestCase
{
    /** @test */
    public function it_can_update_subscription()
    {
        $client = new MockClient([
            UpdateSubscriptionRequest::class => new MockResponse(200, 'subscription'),
        ]);

        $customerId = 'cst_kEn1PlbGa';
        $subscriptionId = 'sub_rVKGtNd6s3';

        $money = new Money('EUR', '20.00');
        $payload = new UpdateSubscriptionPayload(
            $money,
            'Updated subscription',
            '1 month'
        );

        $request = new UpdateSubscriptionRequest($customerId, $subscriptionId, $payload);

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
        $request = new UpdateSubscriptionRequest($customerId, $subscriptionId, new UpdateSubscriptionPayload(
            new Money('EUR', '20.00'),
            'Updated subscription',
            '1 month'
        ));

        $this->assertEquals("customers/{$customerId}/subscriptions/{$subscriptionId}", $request->resolveResourcePath());
    }
}
