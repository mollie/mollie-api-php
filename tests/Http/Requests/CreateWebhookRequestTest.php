<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\CreateWebhookRequest;
use Mollie\Api\Resources\Webhook;
use Mollie\Api\Webhooks\WebhookEventType;
use PHPUnit\Framework\TestCase;

class CreateWebhookRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_webhook()
    {
        $client = new MockMollieClient([
            CreateWebhookRequest::class => MockResponse::created('webhook'),
        ]);

        $request = new CreateWebhookRequest(
            'https://example.org/webhook',
            'order-webhook',
            WebhookEventType::PAYMENT_LINK_PAID
        );

        /** @var Webhook */
        $webhook = $client->send($request);

        $this->assertTrue($webhook->getResponse()->successful());
        $this->assertInstanceOf(Webhook::class, $webhook);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new CreateWebhookRequest(
            'https://example.org/webhook',
            'test-webhook',
            WebhookEventType::PAYMENT_LINK_PAID
        );

        $this->assertEquals('webhooks', $request->resolveResourcePath());
    }
}
