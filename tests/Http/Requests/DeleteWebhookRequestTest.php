<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\DeleteWebhookRequest;
use Mollie\Api\Http\Response;
use PHPUnit\Framework\TestCase;

class DeleteWebhookRequestTest extends TestCase
{
    /** @test */
    public function it_can_delete_webhook()
    {
        $client = new MockMollieClient([
            DeleteWebhookRequest::class => MockResponse::noContent(),
        ]);

        $request = new DeleteWebhookRequest('hook_rHhoN1uzcp');

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertEquals(204, $response->status());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $webhookId = 'hook_rHhoN1uzcp';
        $request = new DeleteWebhookRequest($webhookId);

        $this->assertEquals("webhooks/{$webhookId}", $request->resolveResourcePath());
    }
}
