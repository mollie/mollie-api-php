<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\CancelSubscriptionRequest;
use Mollie\Api\Http\Requests\CreateSubscriptionRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetAllPaginatedSubscriptionsRequest;
use Mollie\Api\Http\Requests\GetPaginatedSubscriptionsRequest;
use Mollie\Api\Http\Requests\GetSubscriptionRequest;
use Mollie\Api\Http\Requests\UpdateSubscriptionRequest;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\Subscription;
use Mollie\Api\Resources\SubscriptionCollection;
use PHPUnit\Framework\TestCase;

class SubscriptionEndpointCollectionTest extends TestCase
{
    /** @test */
    public function create_for()
    {
        $client = new MockMollieClient([
            CreateSubscriptionRequest::class => MockResponse::created('subscription'),
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
    public function get_for()
    {
        $client = new MockMollieClient([
            GetSubscriptionRequest::class => MockResponse::ok('subscription'),
        ]);

        $customer = new Customer($client);
        $customer->id = 'cst_kEn1PlbGa';

        /** @var Subscription $subscription */
        $subscription = $client->subscriptions->getFor($customer, 'sub_rVKGtNd6s3');

        $this->assertSubscription($subscription);
    }

    /** @test */
    public function update_for()
    {
        $client = new MockMollieClient([
            UpdateSubscriptionRequest::class => MockResponse::ok('subscription'),
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
    public function cancel_for()
    {
        $client = new MockMollieClient([
            CancelSubscriptionRequest::class => MockResponse::ok('subscription'),
        ]);

        $customer = new Customer($client);
        $customer->id = 'cst_kEn1PlbGa';

        $client->subscriptions->cancelFor($customer, 'sub_rVKGtNd6s3');

        // Test passes if no exception is thrown
        $this->assertTrue(true);
    }

    /** @test */
    public function page_for()
    {
        $client = new MockMollieClient([
            GetPaginatedSubscriptionsRequest::class => MockResponse::ok('subscription-list'),
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
    public function iterator_for()
    {
        $client = new MockMollieClient([
            GetPaginatedSubscriptionsRequest::class => MockResponse::ok('subscription-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'subscriptions'),
        ]);

        $customer = new Customer($client);
        $customer->id = 'cst_kEn1PlbGa';

        foreach ($client->subscriptions->iteratorFor($customer) as $subscription) {
            $this->assertSubscription($subscription);
        }
    }

    /** @test */
    public function all_for_id()
    {
        $client = new MockMollieClient([
            GetAllPaginatedSubscriptionsRequest::class => MockResponse::ok('subscription-list'),
        ]);

        /** @var SubscriptionCollection $subscriptions */
        $subscriptions = $client->subscriptions->allForId(
            'sub_123',
            50,
            ['profile_id' => 'prf_123']
        );

        $this->assertInstanceOf(SubscriptionCollection::class, $subscriptions);
        $this->assertGreaterThan(0, $subscriptions->count());

        foreach ($subscriptions as $subscription) {
            $this->assertSubscription($subscription);
        }
    }

    /** @test */
    public function iterator_for_all()
    {
        $client = new MockMollieClient([
            GetAllPaginatedSubscriptionsRequest::class => MockResponse::ok('subscription-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'subscriptions'),
        ]);

        foreach (
            $client->subscriptions->iteratorForAll(
                'sub_123',
                50,
                ['profile_id' => 'prf_123'],
                true
            ) as $subscription
        ) {
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
        $this->assertNotEmpty($subscription->webhookUrl);
        $this->assertNotEmpty($subscription->_links);
    }
}
