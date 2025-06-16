<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetWebhookEventRequest;
use Mollie\Api\Resources\WebhookEvent;
use PHPUnit\Framework\TestCase;

class GetWebhookEventRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_webhook_event()
    {
        $client = new MockMollieClient([
            GetWebhookEventRequest::class => MockResponse::ok('webhook-event'),
        ]);

        $request = new GetWebhookEventRequest('event_GvJ8WHrp5isUdRub9CJyH');

        /** @var WebhookEvent */
        $webhookEvent = $client->send($request);

        $this->assertTrue($webhookEvent->getResponse()->successful());
        $this->assertInstanceOf(WebhookEvent::class, $webhookEvent);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $eventId = 'event_GvJ8WHrp5isUdRub9CJyH';
        $request = new GetWebhookEventRequest($eventId);

        $this->assertEquals("events/{$eventId}", $request->resolveResourcePath());
    }
}
