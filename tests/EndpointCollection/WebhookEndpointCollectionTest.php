<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\CreateWebhookRequest;
use Mollie\Api\Http\Requests\DeleteWebhookRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedWebhooksRequest;
use Mollie\Api\Http\Requests\GetWebhookRequest;
use Mollie\Api\Http\Requests\TestWebhookRequest;
use Mollie\Api\Http\Requests\UpdateWebhookRequest;
use Mollie\Api\Resources\AnyResource;
use Mollie\Api\Resources\Webhook;
use Mollie\Api\Resources\WebhookCollection;
use Mollie\Api\Webhooks\WebhookEventType;
use PHPUnit\Framework\TestCase;

class WebhookEndpointCollectionTest extends TestCase
{
    /** @test */
    public function create()
    {
        $client = new MockMollieClient([
            CreateWebhookRequest::class => MockResponse::created('webhook'),
        ]);

        /** @var Webhook $webhook */
        $webhook = $client->webhooks->create([
            'url' => 'https://example.com/webhook',
            'name' => 'My webhook',
            'eventTypes' => WebhookEventType::PAYMENT_LINK_PAID,
        ]);

        $this->assertWebhook($webhook);
    }

    /** @test */
    public function get()
    {
        $client = new MockMollieClient([
            GetWebhookRequest::class => MockResponse::ok('webhook'),
        ]);

        /** @var Webhook $webhook */
        $webhook = $client->webhooks->get('wh_4KgGJJSZpH');

        $this->assertWebhook($webhook);
    }

    /** @test */
    public function update()
    {
        $client = new MockMollieClient([
            UpdateWebhookRequest::class => MockResponse::ok('webhook'),
        ]);

        /** @var Webhook $webhook */
        $webhook = $client->webhooks->update('wh_4KgGJJSZpH', [
            'url' => 'https://example.com/updated-webhook',
            'name' => 'Updated webhook',
            'eventTypes' => WebhookEventType::PAYMENT_LINK_PAID,
        ]);

        $this->assertWebhook($webhook);
    }

    /** @test */
    public function delete()
    {
        $client = new MockMollieClient([
            DeleteWebhookRequest::class => MockResponse::noContent(),
        ]);

        $client->webhooks->delete('wh_4KgGJJSZpH');

        // If we reach this point, the delete was successful
        $this->assertTrue(true);
    }

    /** @test */
    public function test()
    {
        $client = new MockMollieClient([
            TestWebhookRequest::class => MockResponse::ok('webhook-test'),
        ]);

        /** @var AnyResource $result */
        $result = $client->webhooks->test('wh_4KgGJJSZpH');

        $this->assertInstanceOf(AnyResource::class, $result);
    }

    /** @test */
    public function page()
    {
        $client = new MockMollieClient([
            GetPaginatedWebhooksRequest::class => MockResponse::ok('webhook-list'),
        ]);

        /** @var WebhookCollection $webhooks */
        $webhooks = $client->webhooks->page();

        $this->assertInstanceOf(WebhookCollection::class, $webhooks);
        $this->assertGreaterThan(0, $webhooks->count());
        $this->assertGreaterThan(0, count($webhooks));
    }

    /** @test */
    public function iterator()
    {
        $client = new MockMollieClient([
            GetPaginatedWebhooksRequest::class => MockResponse::ok('webhook-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'webhooks'),
        ]);

        foreach ($client->webhooks->iterator() as $webhook) {
            $this->assertWebhook($webhook);
        }
    }

    private function assertWebhook(Webhook $webhook): void
    {
        $this->assertInstanceOf(Webhook::class, $webhook);
        $this->assertEquals('webhook', $webhook->resource);
        $this->assertNotEmpty($webhook->id);
        $this->assertNotEmpty($webhook->url);
        $this->assertNotEmpty($webhook->name);
        $this->assertNotEmpty($webhook->eventTypes);
        $this->assertNotEmpty($webhook->createdAt);
        $this->assertNotEmpty($webhook->_links);
    }
}
