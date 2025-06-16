<?php

namespace Tests\Resources;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Resources\WebhookEvent;
use PHPUnit\Framework\TestCase;

class WebhookEventTest extends TestCase
{
    /** @test */
    public function has_entity_returns_true_when_embedded_entity_exists()
    {
        $client = new MockMollieClient([]);

        $webhookEvent = new WebhookEvent($client);
        $webhookEvent->_embedded = (object) [
            'entity' => (object) [
                'id' => 'pl_4Y0eZitmBnQ5jsBYZIBw',
                'resource' => 'payment-link',
                'status' => 'paid',
            ],
        ];

        $this->assertTrue($webhookEvent->hasEntity());
    }

    /** @test */
    public function has_entity_returns_false_when_embedded_entity_is_null()
    {
        $client = new MockMollieClient([]);

        $webhookEvent = new WebhookEvent($client);
        $webhookEvent->_embedded = (object) [
            'entity' => null,
        ];

        $this->assertFalse($webhookEvent->hasEntity());
    }

    /** @test */
    public function has_entity_returns_false_when_embedded_is_null()
    {
        $client = new MockMollieClient([]);

        $webhookEvent = new WebhookEvent($client);
        $webhookEvent->_embedded = null;

        $this->assertFalse($webhookEvent->hasEntity());
    }

    /** @test */
    public function get_entity_returns_entity_when_embedded_entity_exists()
    {
        $client = new MockMollieClient([]);

        $entity = (object) [
            'id' => 'pl_4Y0eZitmBnQ5jsBYZIBw',
            'resource' => 'payment-link',
            'status' => 'paid',
        ];

        $webhookEvent = new WebhookEvent($client);
        $webhookEvent->_embedded = (object) [
            'entity' => $entity,
        ];

        $this->assertSame($entity, $webhookEvent->getEntity());
    }

    /** @test */
    public function get_entity_returns_null_when_embedded_entity_is_null()
    {
        $client = new MockMollieClient([]);

        $webhookEvent = new WebhookEvent($client);
        $webhookEvent->_embedded = (object) [
            'entity' => null,
        ];

        $this->assertNull($webhookEvent->getEntity());
    }

    /** @test */
    public function get_entity_returns_null_when_embedded_is_null()
    {
        $client = new MockMollieClient([]);

        $webhookEvent = new WebhookEvent($client);
        $webhookEvent->_embedded = null;

        $this->assertNull($webhookEvent->getEntity());
    }

    /** @test */
    public function webhook_event_has_correct_properties()
    {
        $client = new MockMollieClient([]);

        $webhookEvent = new WebhookEvent($client);
        $webhookEvent->resource = 'event';
        $webhookEvent->id = 'whe_gQMMaGqAVA';
        $webhookEvent->type = 'payment-link.paid';
        $webhookEvent->entityId = 'pl_4Y0eZitmBnQ5jsBYZIBw';
        $webhookEvent->createdAt = '2023-12-25T10:30:54+00:00';

        $this->assertEquals('event', $webhookEvent->resource);
        $this->assertEquals('whe_gQMMaGqAVA', $webhookEvent->id);
        $this->assertEquals('payment-link.paid', $webhookEvent->type);
        $this->assertEquals('pl_4Y0eZitmBnQ5jsBYZIBw', $webhookEvent->entityId);
        $this->assertEquals('2023-12-25T10:30:54+00:00', $webhookEvent->createdAt);
    }
}
