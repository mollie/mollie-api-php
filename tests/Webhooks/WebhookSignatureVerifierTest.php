<?php

declare(strict_types=1);

namespace Tests\Webhooks;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Webhooks\WebhookEventMapper;
use Mollie\Api\Webhooks\WebhookSignatureVerifier;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class WebhookSignatureVerifierTest extends TestCase
{
    private const PAYLOAD = '{"id":"evt_test","type":"payment-link.paid","entityId":"pl_test","createdAt":"2024-01-01T00:00:00+00:00"}';

    private const SECRET = 'webhook_secret';

    protected function tearDown(): void
    {
        unset($_ENV['MOLLIE_WEBHOOK_SECRET']);
        putenv('MOLLIE_WEBHOOK_SECRET');

        parent::tearDown();
    }

    #[Test]
    public function valid_signature_returns_true(): void
    {
        $verifier = new WebhookSignatureVerifier;
        $signature = hash_hmac('sha256', self::PAYLOAD, self::SECRET);

        $this->assertTrue($verifier->verify(self::PAYLOAD, $signature, self::SECRET));
    }

    #[Test]
    public function signature_header_matches_mollies_webhook_header(): void
    {
        $this->assertSame('X-Mollie-Signature', WebhookSignatureVerifier::SIGNATURE_HEADER);
    }

    #[Test]
    public function valid_signature_with_sha256_prefix_returns_true(): void
    {
        $verifier = new WebhookSignatureVerifier;
        $signature = 'sha256='.hash_hmac('sha256', self::PAYLOAD, self::SECRET);

        $this->assertTrue($verifier->verify(self::PAYLOAD, $signature, self::SECRET));
    }

    #[Test]
    public function tampered_payload_returns_false(): void
    {
        $verifier = new WebhookSignatureVerifier;
        $signature = hash_hmac('sha256', self::PAYLOAD, self::SECRET);

        $this->assertFalse($verifier->verify(self::PAYLOAD.' ', $signature, self::SECRET));
    }

    #[Test]
    public function wrong_secret_returns_false(): void
    {
        $verifier = new WebhookSignatureVerifier;
        $signature = hash_hmac('sha256', self::PAYLOAD, self::SECRET);

        $this->assertFalse($verifier->verify(self::PAYLOAD, $signature, 'wrong_secret'));
    }

    #[Test]
    public function missing_secret_without_env_throws_invalid_argument_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Webhook secret missing');

        (new WebhookSignatureVerifier)->verify(self::PAYLOAD, 'signature');
    }

    #[Test]
    public function env_fallback_works_when_secret_is_omitted(): void
    {
        putenv('MOLLIE_WEBHOOK_SECRET='.self::SECRET);
        $signature = hash_hmac('sha256', self::PAYLOAD, self::SECRET);

        $this->assertTrue((new WebhookSignatureVerifier)->verify(self::PAYLOAD, $signature));
    }

    #[Test]
    public function verifier_uses_hash_equals_for_constant_time_comparison(): void
    {
        $method = new ReflectionMethod(WebhookSignatureVerifier::class, 'verify');
        $source = file($method->getFileName());
        $body = implode('', array_slice(
            $source,
            $method->getStartLine() - 1,
            $method->getEndLine() - $method->getStartLine() + 1
        ));

        $this->assertStringContainsString('hash_equals', $body);
    }

    #[Test]
    public function client_webhooks_endpoint_exposes_verify_and_mapper(): void
    {
        $client = new MockMollieClient;
        $signature = hash_hmac('sha256', self::PAYLOAD, self::SECRET);

        $this->assertTrue($client->webhooks()->verify(self::PAYLOAD, $signature, self::SECRET));
        $this->assertInstanceOf(WebhookEventMapper::class, $client->webhooks()->mapper());
        $this->assertSame('evt_test', $client->webhooks()->mapper()->process(self::PAYLOAD, $signature)->id);
    }
}
