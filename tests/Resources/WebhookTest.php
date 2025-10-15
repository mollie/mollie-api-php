<?php

namespace Tests\Resources;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\DeleteWebhookRequest;
use Mollie\Api\Http\Requests\TestWebhookRequest;
use Mollie\Api\Http\Requests\UpdateWebhookRequest;
use Mollie\Api\Resources\AnyResource;
use Mollie\Api\Resources\Webhook;
use Mollie\Api\Webhooks\WebhookEventType;
use PHPUnit\Framework\TestCase;

class WebhookTest extends TestCase
{
    /** @test */
    public function update_webhook()
    {
        $client = new MockMollieClient([
            UpdateWebhookRequest::class => MockResponse::ok('webhook'),
        ]);

        $webhook = new Webhook($client);
        $webhook->id = 'wh_4KgGJJSZpH';
        $webhook->url = 'https://example.com/webhook';
        $webhook->name = 'My webhook';
        $webhook->eventTypes = [WebhookEventType::PAYMENT_LINK_PAID];

        /** @var Webhook $updatedWebhook */
        $updatedWebhook = $webhook->update([
            'url' => 'https://example.com/updated-webhook',
            'name' => 'Updated webhook',
        ]);

        $this->assertInstanceOf(Webhook::class, $updatedWebhook);
        $this->assertEquals('webhook', $updatedWebhook->resource);
    }

    /** @test */
    public function delete_webhook()
    {
        $client = new MockMollieClient([
            DeleteWebhookRequest::class => MockResponse::noContent(),
        ]);

        $webhook = new Webhook($client);
        $webhook->id = 'wh_4KgGJJSZpH';

        $webhook->delete();

        // If we reach this point, the delete was successful
        $this->assertTrue(true);
    }

    /** @test */
    public function test_webhook()
    {
        $client = new MockMollieClient([
            TestWebhookRequest::class => MockResponse::ok('webhook-test'),
        ]);

        $webhook = new Webhook($client);
        $webhook->id = 'wh_4KgGJJSZpH';

        /** @var AnyResource $result */
        $result = $webhook->test();

        $this->assertInstanceOf(AnyResource::class, $result);
    }
}
