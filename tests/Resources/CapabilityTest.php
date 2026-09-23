<?php

declare(strict_types=1);

namespace Tests\Resources;

use Mollie\Api\Http\Response;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Capability;
use Mollie\Api\Resources\ResourceHydrator;
use Mollie\Api\Types\CapabilityStatus;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Contract pin mollie/openapi@cfbba47b874b90ea54dc5e2643d163b8bc527902
 * (2026-08-26): entity-capability.required = resource, name, status,
 * statusReason, requirements; statusReason is ["string","null"];
 * capability-status is [unrequested, enabled, disabled, pending];
 * organizationId is not part of the schema.
 */
class CapabilityTest extends TestCase
{
    #[Test]
    public function status_reason_accepts_null(): void
    {
        $capability = $this->hydrate([
            'name' => 'payments',
            'requirements' => [],
            'status' => 'enabled',
            'statusReason' => null,
        ]);

        $this->assertNull($capability->statusReason);
        $this->assertSame(CapabilityStatus::Enabled, $capability->status);
    }

    #[Test]
    public function status_reason_keeps_the_raw_string(): void
    {
        $capability = $this->hydrate([
            'name' => 'payments',
            'requirements' => [],
            'status' => 'pending',
            'statusReason' => 'onboarding-information-needed',
        ]);

        $this->assertSame('onboarding-information-needed', $capability->statusReason);
    }

    #[Test]
    public function status_reason_is_required_but_nullable_so_it_has_no_default(): void
    {
        $property = (new ReflectionClass(Capability::class))->getProperty('statusReason');

        $this->assertFalse($property->hasDefaultValue());
        $this->assertTrue($property->getType()->allowsNull());
    }

    #[Test]
    public function unrequested_is_a_known_status(): void
    {
        $capability = $this->hydrate([
            'name' => 'payments',
            'requirements' => [],
            'status' => 'unrequested',
            'statusReason' => null,
        ]);

        $this->assertSame(CapabilityStatus::Unrequested, $capability->status);
        $this->assertTrue($capability->isUnrequested());
        $this->assertFalse($capability->isEnabled());
        $this->assertFalse($capability->isPending());
        $this->assertFalse($capability->isDisabled());
    }

    #[Test]
    public function organization_id_is_null_when_the_response_omits_it(): void
    {
        $capability = $this->hydrate([
            'name' => 'payments',
            'requirements' => [],
            'status' => 'enabled',
            'statusReason' => null,
        ]);

        $this->assertNull($capability->organizationId);

        $withId = $this->hydrate([
            'name' => 'payments',
            'requirements' => [],
            'status' => 'enabled',
            'statusReason' => null,
            'organizationId' => 'org_12345678',
        ]);

        $this->assertSame('org_12345678', $withId->organizationId);
    }

    /** @param array<string, mixed> $data */
    private function hydrate(array $data): Capability
    {
        $capability = new Capability($this->createMock(MollieApiClient::class));
        (new ResourceHydrator)->hydrate($capability, $data, $this->createMock(Response::class));

        return $capability;
    }
}
