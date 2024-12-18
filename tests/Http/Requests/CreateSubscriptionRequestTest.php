<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Data\CreateSubscriptionPayload;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreateSubscriptionRequest;
use Mollie\Api\Resources\Subscription;
use PHPUnit\Framework\TestCase;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;

class CreateSubscriptionRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_subscription()
    {
        $client = new MockMollieClient([
            CreateSubscriptionRequest::class => new MockResponse(201, 'subscription'),
        ]);

        $request = new CreateSubscriptionRequest('cst_123', new CreateSubscriptionPayload(
            new Money('EUR', '10.00'),
            '1 month',
            'Test subscription'
        ));

        /** @var Subscription */
        $subscription = $client->send($request);

        $this->assertTrue($subscription->getResponse()->successful());
        $this->assertInstanceOf(Subscription::class, $subscription);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new CreateSubscriptionRequest('cst_123', new CreateSubscriptionPayload(
            new Money('EUR', '10.00'),
            '1 month',
            'Test subscription'
        ));

        $this->assertEquals('customers/cst_123/subscriptions', $request->resolveResourcePath());
    }
}
