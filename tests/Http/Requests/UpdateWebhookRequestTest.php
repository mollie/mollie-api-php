<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\UpdateWebhookRequest;
use Mollie\Api\Resources\Webhook;
use Mollie\Api\Webhooks\WebhookEventType;
use PHPUnit\Framework\TestCase;

class UpdateWebhookRequestTest extends TestCase
{
    /** @test */
    public function it_can_update_webhook()
    {
        $client = new MockMollieClient([
            UpdateWebhookRequest::class => MockResponse::ok('webhook'),
        ]);

        $request = new UpdateWebhookRequest(
            'hook_rHhoN1uzcp',
            'https://example.org/new-webhook',
            'updated-webhook',
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
        $webhookId = 'hook_rHhoN1uzcp';
        $request = new UpdateWebhookRequest(
            $webhookId,
            'https://example.org/webhook',
            'test-webhook',
            WebhookEventType::PAYMENT_LINK_PAID
        );

        $this->assertEquals("webhooks/{$webhookId}", $request->resolveResourcePath());
    }
}
