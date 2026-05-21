<?php

declare(strict_types=1);

namespace Mollie\Api\Webhooks;

class WebhookSignatureVerifier
{
    public const SIGNATURE_HEADER = 'X-Mollie-Signature';

    private const SIGNATURE_PREFIX = 'sha256=';

    /**
     * @throws \InvalidArgumentException
     */
    public function verify(string $payload, string $signature, ?string $secret = null): bool
    {
        $secret = $this->resolveSecret($secret);
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        // Mollie sends webhook signatures in X-Mollie-Signature; signed payloads may include sha256=.
        $signature = $this->stripSignaturePrefix($signature);

        return hash_equals($expectedSignature, $signature);
    }

    private function stripSignaturePrefix(string $signature): string
    {
        return str_starts_with($signature, self::SIGNATURE_PREFIX)
            ? substr($signature, strlen(self::SIGNATURE_PREFIX))
            : $signature;
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function resolveSecret(?string $secret): string
    {
        $secret ??= $_ENV['MOLLIE_WEBHOOK_SECRET'] ?? getenv('MOLLIE_WEBHOOK_SECRET') ?: null;

        if ($secret === null || $secret === '') {
            throw new \InvalidArgumentException('Webhook secret missing; pass explicitly or set MOLLIE_WEBHOOK_SECRET.');
        }

        return $secret;
    }
}
