<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Fake\SequenceMockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedWebhooksRequest;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Webhook;
use Mollie\Api\Resources\WebhookCollection;
use PHPUnit\Framework\TestCase;

class GetPaginatedWebhooksRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_webhooks()
    {
        $client = new MockMollieClient([
            GetPaginatedWebhooksRequest::class => MockResponse::ok('webhook-list'),
        ]);

        $request = new GetPaginatedWebhooksRequest;

        /** @var WebhookCollection */
        $webhooks = $client->send($request);

        $this->assertTrue($webhooks->getResponse()->successful());
        $this->assertInstanceOf(WebhookCollection::class, $webhooks);
        $this->assertGreaterThan(0, $webhooks->count());

        foreach ($webhooks as $webhook) {
            $this->assertInstanceOf(Webhook::class, $webhook);
            $this->assertEquals('webhook', $webhook->resource);
        }
    }

    /** @test */
    public function it_can_iterate_over_webhooks()
    {
        $client = MollieApiClient::fake([
            GetPaginatedWebhooksRequest::class => MockResponse::ok('webhook-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                MockResponse::ok('webhook-list'),
                MockResponse::ok('empty-list', 'webhooks'),
            ),
        ]);

        $request = (new GetPaginatedWebhooksRequest)->useIterator();

        /** @var LazyCollection */
        $webhooks = $client->send($request);
        $this->assertTrue($webhooks->getResponse()->successful());

        foreach ($webhooks as $webhook) {
            $this->assertInstanceOf(Webhook::class, $webhook);
        }

        $client->assertSentCount(3);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetPaginatedWebhooksRequest;

        $this->assertEquals('webhooks', $request->resolveResourcePath());
    }
}
