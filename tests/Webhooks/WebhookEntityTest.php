<?php

namespace Tests\Webhooks;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaymentLinkRequest;
use Mollie\Api\Resources\AnyResource;
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
    public function as_resource_uses_specific_get_request_when_available()
    {
        $client = new MockMollieClient([
            GetPaymentLinkRequest::class => MockResponse::ok('payment-link', 'pl_4Y0eZitmBnQ5jsBYZIBw'),
        ]);

        $entity = WebhookEntity::create([
            'id' => 'pl_4Y0eZitmBnQ5jsBYZIBw',
            'resource' => 'payment-link',
            'mode' => 'live',
        ]);

        $resource = $entity->asResource($client);

        $this->assertInstanceOf(PaymentLink::class, $resource);

        $client->assertSent(GetPaymentLinkRequest::class);
    }

    /** @test */
    public function as_resource_respects_test_mode()
    {
        $client = new MockMollieClient([
            GetPaymentLinkRequest::class => MockResponse::ok('payment-link', 'pl_4Y0eZitmBnQ5jsBYZIBw'),
        ]);

        $entity = WebhookEntity::create([
            'id' => 'pl_4Y0eZitmBnQ5jsBYZIBw',
            'resource' => 'payment-link',
            'mode' => 'test',
        ]);

        $resource = $entity->asResource($client);

        $this->assertInstanceOf(PaymentLink::class, $resource);

        $client->assertSent(function ($pendingRequest) {
            /** @var GetPaymentLinkRequest $request */
            $request = $pendingRequest->getRequest();

            return $request->getTestmode() === true;
        });
    }

    /** @test */
    public function as_resource_uses_self_href_when_available()
    {
        $client = new MockMollieClient([
            DynamicGetRequest::class => MockResponse::ok([], 'pl_4Y0eZitmBnQ5jsBYZIBw'),
        ]);

        $entity = WebhookEntity::create([
            'id' => 'pl_4Y0eZitmBnQ5jsBYZIBw',
            'resource' => 'foo-resource',
            'mode' => 'live',
            '_links' => [
                'self' => [
                    'href' => 'https://api.mollie.com/v2/foo-resources/pl_4Y0eZitmBnQ5jsBYZIBw',
                    'type' => 'application/hal+json',
                ],
            ],
        ]);

        $resource = $entity->asResource($client);

        $this->assertInstanceOf(AnyResource::class, $resource);
    }

    /** @test */
    public function as_resource_builds_fallback_href_when_no_specific_request_and_no_self_href()
    {
        $client = new MockMollieClient([
            DynamicGetRequest::class => MockResponse::ok([], ''),
        ]);

        // Using a resource type that doesn't have a specific Get request
        $entity = WebhookEntity::create([
            'id' => 'unknown_123',
            'resource' => 'unknown-resource',
            'mode' => 'live',
        ]);

        $resource = $entity->asResource($client);

        // Should return AnyResource as fallback
        $this->assertInstanceOf(AnyResource::class, $resource);
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
