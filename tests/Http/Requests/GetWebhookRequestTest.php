<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetWebhookRequest;
use Mollie\Api\Resources\Webhook;
use PHPUnit\Framework\TestCase;

class GetWebhookRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_webhook()
    {
        $client = new MockMollieClient([
            GetWebhookRequest::class => MockResponse::ok('webhook'),
        ]);

        $request = new GetWebhookRequest('hook_rHhoN1uzcp');

        /** @var Webhook */
        $webhook = $client->send($request);

        $this->assertTrue($webhook->getResponse()->successful());
        $this->assertInstanceOf(Webhook::class, $webhook);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $webhookId = 'hook_rHhoN1uzcp';
        $request = new GetWebhookRequest($webhookId);

        $this->assertEquals("webhooks/{$webhookId}", $request->resolveResourcePath());
    }
}
