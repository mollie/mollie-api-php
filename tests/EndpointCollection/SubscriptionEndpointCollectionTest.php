<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Requests\CreateSubscriptionRequest;
use Mollie\Api\Http\Requests\GetSubscriptionRequest;
use Mollie\Api\Http\Requests\GetPaginatedSubscriptionsRequest;
use Mollie\Api\Http\Requests\UpdateSubscriptionRequest;
use Mollie\Api\Http\Requests\CancelSubscriptionRequest;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\Subscription;
use Mollie\Api\Resources\SubscriptionCollection;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class SubscriptionEndpointCollectionTest extends TestCase
{
    /** @test */
    public function create_for_test()
    {
        $client = new MockClient([
            CreateSubscriptionRequest::class => new MockResponse(201, 'subscription'),
        ]);

        $customer = new Customer($client);
        $customer->id = 'cst_kEn1PlbGa';

        /** @var Subscription $subscription */
        $subscription = $client->subscriptions->createFor($customer, [
            'amount' => [
                'currency' => 'EUR',
                'value' => '25.00',
            ],
            'interval' => '1 month',
            'description' => 'Monthly subscription',
            'webhookUrl' => 'https://example.org/webhook',
        ]);

        $this->assertSubscription($subscription);
    }

    /** @test */
    public function get_for_test()
    {
        $client = new MockClient([
            GetSubscriptionRequest::class => new MockResponse(200, 'subscription'),
        ]);

        $customer = new Customer($client);
        $customer->id = 'cst_kEn1PlbGa';

        /** @var Subscription $subscription */
        $subscription = $client->subscriptions->getFor($customer, 'sub_rVKGtNd6s3');

        $this->assertSubscription($subscription);
    }

    /** @test */
    public function update_for_test()
    {
        $client = new MockClient([
            UpdateSubscriptionRequest::class => new MockResponse(200, 'subscription'),
        ]);

        $customer = new Customer($client);
        $customer->id = 'cst_kEn1PlbGa';

        /** @var Subscription $subscription */
        $subscription = $client->subscriptions->update($customer->id, 'sub_rVKGtNd6s3', [
            'amount' => [
                'currency' => 'EUR',
                'value' => '30.00',
            ],
            'description' => 'Updated subscription',
        ]);

        $this->assertSubscription($subscription);
    }

    /** @test */
    public function cancel_for_test()
    {
        $client = new MockClient([
            CancelSubscriptionRequest::class => new MockResponse(204),
        ]);

        $customer = new Customer($client);
        $customer->id = 'cst_kEn1PlbGa';

        $client->subscriptions->cancelFor($customer, 'sub_rVKGtNd6s3');

        // Test passes if no exception is thrown
        $this->assertTrue(true);
    }

    /** @test */
    public function page_for_test()
    {
        $client = new MockClient([
            GetPaginatedSubscriptionsRequest::class => new MockResponse(200, 'subscription-list'),
        ]);

        $customer = new Customer($client);
        $customer->id = 'cst_kEn1PlbGa';

        /** @var SubscriptionCollection $subscriptions */
        $subscriptions = $client->subscriptions->pageFor($customer);

        $this->assertInstanceOf(SubscriptionCollection::class, $subscriptions);
        $this->assertGreaterThan(0, $subscriptions->count());
        $this->assertGreaterThan(0, count($subscriptions));

        foreach ($subscriptions as $subscription) {
            $this->assertSubscription($subscription);
        }
    }

    /** @test */
    public function iterator_for_test()
    {
        $client = new MockClient([
            GetPaginatedSubscriptionsRequest::class => new MockResponse(200, 'subscription-list'),
        ]);

        $customer = new Customer($client);
        $customer->id = 'cst_kEn1PlbGa';

        foreach ($client->subscriptions->iteratorFor($customer) as $subscription) {
            $this->assertInstanceOf(Subscription::class, $subscription);
            $this->assertSubscription($subscription);
        }
    }

    protected function assertSubscription(Subscription $subscription)
    {
        $this->assertInstanceOf(Subscription::class, $subscription);
        $this->assertEquals('subscription', $subscription->resource);
        $this->assertNotEmpty($subscription->id);
        $this->assertNotEmpty($subscription->mode);
        $this->assertNotEmpty($subscription->createdAt);
        $this->assertNotEmpty($subscription->status);
        $this->assertNotEmpty($subscription->amount);
        $this->assertNotEmpty($subscription->times);
        $this->assertNotEmpty($subscription->interval);
        $this->assertNotEmpty($subscription->description);
        $this->assertNotEmpty($subscription->method);
        $this->assertNotEmpty($subscription->webhookUrl);
        $this->assertNotEmpty($subscription->_links);
    }
}
