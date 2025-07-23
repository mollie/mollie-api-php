<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetWebhookEventRequest;
use Mollie\Api\Resources\WebhookEvent;
use PHPUnit\Framework\TestCase;

class WebhookEventEndpointCollectionTest extends TestCase
{
    /** @test */
    public function get()
    {
        $client = new MockMollieClient([
            GetWebhookEventRequest::class => MockResponse::ok('webhook-event'),
        ]);

        /** @var WebhookEvent $webhookEvent */
        $webhookEvent = $client->webhookEvents->get('event_GvJ8WHrp5isUdRub9CJyH');

        $this->assertInstanceOf(WebhookEvent::class, $webhookEvent);
    }
}
