<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Data\CreateSubscriptionPayload;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreateSubscriptionRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Subscription;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class CreateSubscriptionRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_subscription()
    {
        $client = new MockClient([
            CreateSubscriptionRequest::class => new MockResponse(201, 'subscription'),
        ]);

        $request = new CreateSubscriptionRequest('cst_123', new CreateSubscriptionPayload(
            new Money('EUR', '10.00'),
            '1 month',
            'Test subscription'
        ));

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(Subscription::class, $response->toResource());
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
