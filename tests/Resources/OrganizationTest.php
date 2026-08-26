<?php

declare(strict_types=1);

namespace Tests\Resources;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\Address;
use Mollie\Api\Http\Requests\GetOrganizationRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Organization;
use Mollie\Api\Resources\ResourceHydrator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class OrganizationTest extends TestCase
{
    /**
     * The required members of GET /v2/organizations/me per the OpenAPI spec
     * (mollie/openapi@cfbba47b874b90ea54dc5e2643d163b8bc527902, 2026-08-26):
     * _links, email, id, locale, name, resource. `locale` is non-null there:
     * the property intersects the shared nullable `locale-response` schema
     * with `type: string`.
     */
    private const SPEC_MINIMUM = [
        'resource' => 'organization',
        'id' => 'org_12345678',
        'name' => 'Mollie B.V.',
        'email' => 'info@mollie.com',
        'locale' => 'nl_NL',
        '_links' => [],
    ];

    #[Test]
    public function it_hydrates_the_spec_minimum_payload_and_reads_every_typed_property(): void
    {
        $organization = $this->hydrate(self::SPEC_MINIMUM);

        $this->assertSame('org_12345678', $organization->id);
        $this->assertSame('Mollie B.V.', $organization->name);
        $this->assertSame('info@mollie.com', $organization->email);
        $this->assertSame('nl_NL', $organization->locale);
        $this->assertNull($organization->address);
        $this->assertNull($organization->registrationNumber);
        $this->assertNull($organization->vatNumber);
        $this->assertNull($organization->vatRegulation);
    }

    #[Test]
    public function it_accepts_explicit_nulls_for_nullable_fields(): void
    {
        $organization = $this->hydrate(self::SPEC_MINIMUM + [
            'registrationNumber' => null,
            'vatNumber' => null,
            'vatRegulation' => null,
        ]);

        $this->assertNull($organization->registrationNumber);
        $this->assertNull($organization->vatNumber);
        $this->assertNull($organization->vatRegulation);
    }

    #[Test]
    public function it_hydrates_a_full_address_into_the_value_object(): void
    {
        $organization = $this->hydrate(self::SPEC_MINIMUM + [
            'address' => [
                'streetAndNumber' => 'Keizersgracht 126',
                'postalCode' => '1015 CW',
                'city' => 'Amsterdam',
                'country' => 'NL',
            ],
            'registrationNumber' => '30204462',
            'vatNumber' => 'NL815839091B01',
        ]);

        $this->assertInstanceOf(Address::class, $organization->address);
        $this->assertSame('Amsterdam', $organization->address->city);
        $this->assertSame('30204462', $organization->registrationNumber);
        $this->assertSame('NL815839091B01', $organization->vatNumber);
    }

    #[Test]
    public function spec_required_fields_keep_no_default_and_stay_non_null_so_malformed_responses_stay_visible(): void
    {
        $reflection = new ReflectionClass(Organization::class);

        foreach (['id', 'name', 'email', 'locale'] as $required) {
            $property = $reflection->getProperty($required);

            $this->assertFalse($property->hasDefaultValue(), "Organization::\${$required} must not have a default");
            $this->assertFalse($property->getType()->allowsNull(), "Organization::\${$required} is non-null per the contract");
        }
    }

    #[Test]
    public function the_bundled_fixture_still_hydrates(): void
    {
        $client = new MockMollieClient([
            GetOrganizationRequest::class => MockResponse::ok('organization'),
        ]);

        $organization = $client->send(new GetOrganizationRequest('me'));

        $this->assertInstanceOf(Address::class, $organization->address);
        $this->assertSame('30204462', $organization->registrationNumber);
    }

    /** @param array<string, mixed> $data */
    private function hydrate(array $data): Organization
    {
        $organization = new Organization($this->createMock(MollieApiClient::class));
        (new ResourceHydrator)->hydrate($organization, $data, $this->createMock(Response::class));

        return $organization;
    }
}
