<?php

namespace Tests\Webhooks;

use DateTimeImmutable;
use Mollie\Api\Exceptions\MissingAuthenticationException;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentChargebacksRequest;
use Mollie\Api\Http\Requests\GetPaginatedSubscriptionPaymentsRequest;
use Mollie\Api\Http\Adapter\CurlMollieHttpAdapter;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\AnyResource;
use Mollie\Api\Resources\BalanceTransaction;
use Mollie\Api\Resources\Profile;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentLink;
use Mollie\Api\Resources\Subscription;
use Mollie\Api\Webhooks\WebhookEntity;
use Mollie\Api\Webhooks\WebhookSnapshotOrigin;
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
        $this->assertEquals((object) ['currency' => 'EUR', 'value' => '10.00'], $resource->amount);
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
    public function as_resource_exposes_webhook_snapshot_origin_when_supplied()
    {
        $client = new MockMollieClient;
        $origin = new WebhookSnapshotOrigin(
            $client,
            'event_abc',
            'sig_xyz',
            new DateTimeImmutable('2026-04-17T12:00:00+00:00')
        );

        $entity = WebhookEntity::create([
            'id' => 'pl_4Y0eZitmBnQ5jsBYZIBw',
            'resource' => 'payment-link',
            'mode' => 'live',
        ]);

        $resource = $entity->asResource($client, $origin);

        $this->assertInstanceOf(PaymentLink::class, $resource);
        $this->assertNull($resource->getResponse());
        $this->assertSame($origin, $resource->getOrigin());
        $this->assertInstanceOf(WebhookSnapshotOrigin::class, $resource->getOrigin());
        $this->assertSame('event_abc', $resource->getOrigin()->getEventId());
        $this->assertSame('sig_xyz', $resource->getOrigin()->getSignature());
        $this->assertEquals(
            new DateTimeImmutable('2026-04-17T12:00:00+00:00'),
            $resource->getOrigin()->getReceivedAt()
        );
        $client->assertSentCount(0);
    }

    /** @test */
    public function as_resource_builds_fallback_origin_when_none_supplied()
    {
        // Preserves backward compatibility with the single-arg call form
        // used in docs/webhooks.md and ad-hoc utility scripts.
        $client = new MockMollieClient;

        $entity = WebhookEntity::create([
            'id' => 'pl_4Y0eZitmBnQ5jsBYZIBw',
            'resource' => 'payment-link',
            'mode' => 'live',
        ]);

        $resource = $entity->asResource($client);

        $this->assertInstanceOf(PaymentLink::class, $resource);
        $this->assertInstanceOf(WebhookSnapshotOrigin::class, $resource->getOrigin());
        $this->assertSame('unknown', $resource->getOrigin()->getEventId());
        $this->assertNull($resource->getOrigin()->getSignature());
        $this->assertNull($resource->getResponse());
        $client->assertSentCount(0);
    }

    /** @test */
    public function payment_chargebacks_fallback_keeps_testmode_on_webhook_origin()
    {
        $client = new MockMollieClient([
            GetPaginatedPaymentChargebacksRequest::class => MockResponse::ok('chargeback-list'),
        ], true);

        $entity = WebhookEntity::create([
            'id' => 'tr_testmode',
            'resource' => 'payment',
            'mode' => 'test',
        ]);

        /** @var Payment $payment */
        $payment = $entity->asResource($client);
        $payment->chargebacks();

        $client->assertSent(function (PendingRequest $pendingRequest) {
            $this->assertSame('testmode=true', $pendingRequest->getUri()->getQuery());

            return true;
        });
    }

    /** @test */
    public function subscription_payments_fallback_keeps_testmode_on_webhook_origin()
    {
        $client = new MockMollieClient([
            GetPaginatedSubscriptionPaymentsRequest::class => MockResponse::ok('payment-list'),
        ], true);

        $entity = WebhookEntity::create([
            'id' => 'sub_testmode',
            'resource' => 'subscription',
            'customerId' => 'cst_testmode',
            'mode' => 'test',
        ]);

        /** @var Subscription $subscription */
        $subscription = $entity->asResource($client);
        $subscription->payments();

        $client->assertSent(function (PendingRequest $pendingRequest) {
            $this->assertSame('testmode=true', $pendingRequest->getUri()->getQuery());

            return true;
        });
    }

    /** @test */
    public function profile_fallback_requests_keep_testmode_on_webhook_origin()
    {
        $client = new MockMollieClient([
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'refunds'),
        ], true);

        $entity = WebhookEntity::create([
            'id' => 'pfl_testmode',
            'resource' => 'profile',
            'mode' => 'test',
        ]);

        /** @var Profile $profile */
        $profile = $entity->asResource($client);
        $profile->refunds();

        $client->assertSent(function (PendingRequest $pendingRequest) {
            $this->assertSame('testmode=true', $pendingRequest->getUri()->getQuery());

            return true;
        });
    }

    /** @test */
    public function as_resource_works_without_authenticator_on_connector()
    {
        // Webhook hydration is self-sufficient — no API key required.
        $client = new MollieApiClient(new CurlMollieHttpAdapter);

        $entity = WebhookEntity::create([
            'id' => 'pl_x',
            'resource' => 'payment-link',
            'mode' => 'live',
        ]);

        $resource = $entity->asResource($client);

        $this->assertInstanceOf(PaymentLink::class, $resource);
    }

    /** @test */
    public function as_resource_returns_any_resource_with_accessible_custom_fields()
    {
        $client = new MockMollieClient;

        $entity = WebhookEntity::create([
            'id' => 'unknown_123',
            'resource' => 'unknown-resource',
            'mode' => 'live',
            'custom_field' => 'custom_value',
            'nested' => ['key' => 'value'],
        ]);

        $resource = $entity->asResource($client);

        $this->assertInstanceOf(AnyResource::class, $resource);
        $this->assertSame('custom_value', $resource->custom_field);
        // Nested values arrive as arrays for AnyResource::fill() since the
        // snapshot hydrator round-trips through json_decode (same shape the
        // HTTP pipeline produces for AnyResource).
        $this->assertSame(['key' => 'value'], (array) $resource->nested);
    }

    /** @test */
    public function follow_up_calls_on_webhook_origin_require_authenticator()
    {
        // Webhook hydration works without an API key, but follow-up calls
        // into the connector still run auth middleware. Documenting the
        // failure mode explicitly.
        $client = new MollieApiClient(new CurlMollieHttpAdapter);

        $entity = WebhookEntity::create([
            'id' => 'tr_x',
            'resource' => 'payment',
            'mode' => 'live',
            '_links' => [
                'refunds' => ['href' => 'https://api.mollie.com/v2/payments/tr_x/refunds'],
            ],
        ]);

        /** @var \Mollie\Api\Resources\Payment $payment */
        $payment = $entity->asResource($client);

        $this->expectException(MissingAuthenticationException::class);
        $payment->refunds();
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
