<?php

namespace Tests\Webhooks;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Resources\AnyResource;
use Mollie\Api\Resources\BalanceTransaction;
use Mollie\Api\Resources\PaymentLink;
use Mollie\Api\Webhooks\WebhookEntity;
use PHPUnit\Framework\TestCase;

class WebhookEntityTest extends TestCase
{
    /** @test */
    public function creates_webhook_entity_from_array()
    {
        $entity = WebhookEntity::create([
            'id' => 'pl_4Y0eZitmBnQ5jsBYZIBw',
            'resource' => 'payment-link',
            'status' => 'paid',
            'mode' => 'test',
        ]);

        $this->assertEquals('payment-link', $entity->getResourceType());
        $this->assertEquals('pl_4Y0eZitmBnQ5jsBYZIBw', $entity->getId());
        $this->assertEquals('paid', $entity->getData('status'));
        $this->assertTrue($entity->isInTestmode());
    }

    /** @test */
    public function creates_webhook_entity_from_object()
    {
        $entityData = (object) [
            'id' => 'pl_4Y0eZitmBnQ5jsBYZIBw',
            'resource' => 'payment-link',
            'status' => 'paid',
            'mode' => 'live',
        ];

        $entity = WebhookEntity::create($entityData);

        $this->assertEquals('payment-link', $entity->getResourceType());
        $this->assertEquals('pl_4Y0eZitmBnQ5jsBYZIBw', $entity->getId());
        $this->assertEquals('paid', $entity->getData('status'));
        $this->assertFalse($entity->isInTestmode());
    }

    /** @test */
    public function as_resource_hydrates_locally_from_snapshot_without_http_call()
    {
        $client = new MockMollieClient;

        $entity = WebhookEntity::create([
            'id' => 'pl_4Y0eZitmBnQ5jsBYZIBw',
            'resource' => 'payment-link',
            'mode' => 'live',
            'description' => 'Test payment link',
            'amount' => [
                'currency' => 'EUR',
                'value' => '10.00',
            ],
        ]);

        $resource = $entity->asResource($client);

        $this->assertInstanceOf(PaymentLink::class, $resource);
        $this->assertEquals('pl_4Y0eZitmBnQ5jsBYZIBw', $resource->id);
        $this->assertEquals('Test payment link', $resource->description);
        $this->assertEquals(['currency' => 'EUR', 'value' => '10.00'], $resource->amount);
        $client->assertSentCount(0);
    }

    /** @test */
    public function as_resource_hydrates_sub_resources_without_top_level_endpoint()
    {
        // BalanceTransaction has no top-level GET endpoint — only listed under
        // /balances/{id}/transactions. Hydrating from the signed snapshot must
        // not attempt a 404-bound HTTP fetch.
        $client = new MockMollieClient;

        $entity = WebhookEntity::create([
            'id' => 'baltr_QM24QwzUWR4ev4Xfgyt29d',
            'resource' => 'balance_transaction',
            'mode' => 'live',
            'type' => 'payment',
            'createdAt' => '2024-12-25T10:30:54+00:00',
            'resultAmount' => [
                'currency' => 'EUR',
                'value' => '100.00',
            ],
        ]);

        $resource = $entity->asResource($client);

        $this->assertInstanceOf(BalanceTransaction::class, $resource);
        $this->assertEquals('baltr_QM24QwzUWR4ev4Xfgyt29d', $resource->id);
        $this->assertEquals('payment', $resource->type);
        $client->assertSentCount(0);
    }

    /** @test */
    public function as_resource_returns_any_resource_for_unknown_resource_types()
    {
        $client = new MockMollieClient;

        $entity = WebhookEntity::create([
            'id' => 'unknown_123',
            'resource' => 'unknown-resource',
            'mode' => 'live',
            'custom_field' => 'custom_value',
        ]);

        $resource = $entity->asResource($client);

        $this->assertInstanceOf(AnyResource::class, $resource);
        $client->assertSentCount(0);
    }

    /** @test */
    public function as_resource_exposes_snapshot_via_response_body()
    {
        $client = new MockMollieClient;

        $entity = WebhookEntity::create([
            'id' => 'pl_4Y0eZitmBnQ5jsBYZIBw',
            'resource' => 'payment-link',
            'status' => 'paid',
            'mode' => 'live',
        ]);

        $resource = $entity->asResource($client);

        $this->assertTrue($resource->getResponse()->successful());
        $this->assertEquals('pl_4Y0eZitmBnQ5jsBYZIBw', $resource->getResponse()->json()->id);
    }

    /** @test */
    public function get_data_returns_all_data_when_no_key_provided()
    {
        $entityData = [
            'id' => 'pl_4Y0eZitmBnQ5jsBYZIBw',
            'resource' => 'payment-link',
            'status' => 'paid',
            'amount' => [
                'currency' => 'EUR',
                'value' => '10.00',
            ],
        ];

        $entity = WebhookEntity::create($entityData);

        $this->assertEquals($entityData, $entity->getData());
    }

    /** @test */
    public function get_data_returns_specific_value_when_key_provided()
    {
        $entity = WebhookEntity::create([
            'id' => 'pl_4Y0eZitmBnQ5jsBYZIBw',
            'resource' => 'payment-link',
            'status' => 'paid',
            'amount' => [
                'currency' => 'EUR',
                'value' => '10.00',
            ],
        ]);

        $this->assertEquals('paid', $entity->getData('status'));
    }

    /** @test */
    public function get_data_supports_dot_notation()
    {
        $entity = WebhookEntity::create([
            'id' => 'pl_4Y0eZitmBnQ5jsBYZIBw',
            'resource' => 'payment-link',
            'amount' => [
                'currency' => 'EUR',
                'value' => '10.00',
            ],
        ]);

        $this->assertEquals('EUR', $entity->getData('amount.currency'));
        $this->assertEquals('10.00', $entity->getData('amount.value'));
    }

    /** @test */
    public function get_resource_type_returns_resource_type()
    {
        $entity = WebhookEntity::create([
            'id' => 'pl_4Y0eZitmBnQ5jsBYZIBw',
            'resource' => 'payment-link',
        ]);

        $this->assertEquals('payment-link', $entity->getResourceType());
    }

    /** @test */
    public function get_id_returns_id()
    {
        $entity = WebhookEntity::create([
            'id' => 'pl_4Y0eZitmBnQ5jsBYZIBw',
            'resource' => 'payment-link',
        ]);

        $this->assertEquals('pl_4Y0eZitmBnQ5jsBYZIBw', $entity->getId());
    }

    /** @test */
    public function is_in_testmode_returns_true_when_mode_is_test()
    {
        $entity = WebhookEntity::create([
            'id' => 'pl_4Y0eZitmBnQ5jsBYZIBw',
            'resource' => 'payment-link',
            'mode' => 'test',
        ]);

        $this->assertTrue($entity->isInTestmode());
    }

    /** @test */
    public function is_in_testmode_returns_false_when_mode_is_live()
    {
        $entity = WebhookEntity::create([
            'id' => 'pl_4Y0eZitmBnQ5jsBYZIBw',
            'resource' => 'payment-link',
            'mode' => 'live',
        ]);

        $this->assertFalse($entity->isInTestmode());
    }

    /** @test */
    public function is_in_testmode_returns_false_when_mode_is_not_set()
    {
        $entity = WebhookEntity::create([
            'id' => 'pl_4Y0eZitmBnQ5jsBYZIBw',
            'resource' => 'payment-link',
        ]);

        $this->assertFalse($entity->isInTestmode());
    }
}
