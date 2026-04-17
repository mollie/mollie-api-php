<?php

namespace Tests\Webhooks;

use Mollie\Api\Utils\Arr;
use PHPUnit\Framework\TestCase;

/**
 * Guards the SDK against regressions where real Mollie webhook POST
 * deliveries would no longer round-trip through the hydration pipeline.
 *
 * Real webhook POST payloads key the embedded entity by **resource type**
 * (e.g. `_embedded["payment-link"]`), not by the literal key `"entity"`
 * used by the `GET /v2/events/{id}` API. Any webhook handling code must
 * iterate `_embedded` keys rather than assuming a fixed `.entity` shape.
 *
 * Fixtures are committed under tests/Fixtures/Webhooks/payloads/ and
 * sourced from https://staging.docs.mollie.com/reference/webhooks.
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
        foreach ($embedded as $key => $candidate) {
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
    public function full_payload_embedded_entity_is_keyed_by_resource_type()
    {
        $payload = $this->loadFixture('payment-link-paid.full.json');

        $embedded = Arr::get($payload, '_embedded', []);

        $this->assertArrayHasKey(
            'payment-link',
            $embedded,
            'Real Mollie webhook POST payloads key the embedded entity by '
            .'resource type (e.g. "payment-link"), not by the literal key '
            .'"entity". Webhook handling code must iterate _embedded keys.'
        );

        $this->assertArrayNotHasKey(
            'entity',
            $embedded,
            'Real webhook POST payloads do NOT use _embedded.entity — that '
            .'shape is specific to the GET /v2/events/{id} API response.'
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
