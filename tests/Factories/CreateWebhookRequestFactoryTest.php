<?php

namespace Tests\Factories;

use Mollie\Api\Factories\CreateWebhookRequestFactory;
use Mollie\Api\Http\Requests\CreateWebhookRequest;
use Mollie\Api\Webhooks\WebhookEventType;
use PHPUnit\Framework\TestCase;

class CreateWebhookRequestFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_webhook_request_object_with_full_data()
    {
        $request = CreateWebhookRequestFactory::new()
            ->withPayload([
                'url' => 'https://example.com/webhook',
                'name' => 'Test webhook',
                'eventTypes' => WebhookEventType::PAYMENT_LINK_PAID,
            ])
            ->create();

        $this->assertInstanceOf(CreateWebhookRequest::class, $request);
        $this->assertEquals('webhooks', $request->resolveResourcePath());
        $this->assertEquals('POST', $request->getMethod());
    }

    /** @test */
    public function create_returns_webhook_request_object_with_minimal_data()
    {
        $request = CreateWebhookRequestFactory::new()
            ->withPayload([
                'url' => 'https://example.com/webhook',
                'name' => 'Test webhook',
                'eventTypes' => WebhookEventType::PAYMENT_LINK_PAID,
            ])
            ->create();

        $this->assertInstanceOf(CreateWebhookRequest::class, $request);
        $this->assertEquals('webhooks', $request->resolveResourcePath());
        $this->assertEquals('POST', $request->getMethod());
    }
}
