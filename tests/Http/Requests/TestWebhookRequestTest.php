<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\TestWebhookRequest;
use Mollie\Api\Resources\AnyResource;
use PHPUnit\Framework\TestCase;

class TestWebhookRequestTest extends TestCase
{
    /** @test */
    public function it_can_test_webhook()
    {
        $client = new MockMollieClient([
            TestWebhookRequest::class => MockResponse::ok('webhook-test'),
        ]);

        $request = new TestWebhookRequest('hook_rHhoN1uzcp');

        /** @var AnyResource */
        $result = $client->send($request);

        $this->assertTrue($result->getResponse()->successful());
        $this->assertInstanceOf(AnyResource::class, $result);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $webhookId = 'hook_rHhoN1uzcp';
        $request = new TestWebhookRequest($webhookId);

        $this->assertEquals("webhooks/{$webhookId}/ping", $request->resolveResourcePath());
    }
}
