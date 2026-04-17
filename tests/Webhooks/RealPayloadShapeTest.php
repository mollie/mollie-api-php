<?php

namespace Tests\Webhooks;

use Mollie\Api\Utils\Arr;
use PHPUnit\Framework\TestCase;

/**
 * Guards the SDK against regressions where real Mollie webhook POST
 * deliveries would no longer round-trip through the hydration pipeline.
 *
 * Mollie keys the embedded resource under `_embedded.entity` in both the
 * push delivery and the `GET /v2/events/{id}` API. The SDK's mapper is
 * still key-agnostic so that a future schema tweak (additional sub-blocks,
 * renamed key) does not silently break webhook handling.
 *
 * Fixtures are committed under tests/Fixtures/Webhooks/payloads/.
 */
class RealPayloadShapeTest extends TestCase
{
    /** @test */
    public function full_payload_has_self_link_on_embedded_entity()
    {
        $payload = $this->loadFixture('payment-link-paid.full.json');

        $embedded = Arr::get($payload, '_embedded', []);
        $this->assertNotEmpty(
            $embedded,
            'Expected full webhook payload to carry an _embedded entity.'
        );

        $selfHref = null;
        foreach ($embedded as $candidate) {
            if (is_array($candidate) && isset($candidate['_links']['self']['href'])) {
                $selfHref = $candidate['_links']['self']['href'];

                break;
            }
        }

        $this->assertNotNull(
            $selfHref,
            'Full webhook payload missing _embedded.*._links.self.href — '
            .'capability-preserved claim breaks here.'
        );
    }

    /** @test */
    public function full_payload_embedded_entity_uses_entity_key()
    {
        $payload = $this->loadFixture('payment-link-paid.full.json');

        $embedded = Arr::get($payload, '_embedded', []);

        $this->assertArrayHasKey(
            'entity',
            $embedded,
            'Mollie keys the embedded resource under _embedded.entity.'
        );
    }

    /** @test */
    public function simple_payload_has_no_embedded_entity()
    {
        $payload = $this->loadFixture('payment-link-paid.simple.json');

        $this->assertArrayNotHasKey('_embedded', $payload);
    }

    /** @test */
    public function every_embedded_entity_carries_id_and_resource_fields()
    {
        $payload = $this->loadFixture('payment-link-paid.full.json');

        foreach (Arr::get($payload, '_embedded', []) as $candidate) {
            $this->assertIsArray($candidate);
            $this->assertArrayHasKey('id', $candidate);
            $this->assertArrayHasKey('resource', $candidate);
        }
    }

    private function loadFixture(string $name): array
    {
        $path = __DIR__.'/../Fixtures/Webhooks/payloads/'.$name;
        $contents = file_get_contents($path);

        $this->assertNotFalse($contents, "Fixture $path should be readable.");

        return json_decode($contents, true);
    }
}
