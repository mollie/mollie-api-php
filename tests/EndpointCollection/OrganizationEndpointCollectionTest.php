<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetOrganizationPartnerStatusRequest;
use Mollie\Api\Http\Requests\GetOrganizationRequest;
use Mollie\Api\Resources\Organization;
use Mollie\Api\Resources\Partner;
use PHPUnit\Framework\TestCase;

class OrganizationEndpointCollectionTest extends TestCase
{
    /** @test */
    public function get()
    {
        $client = new MockMollieClient([
            GetOrganizationRequest::class => MockResponse::ok('organization', 'org_12345678'),
        ]);

        /** @var Organization $organization */
        $organization = $client->organizations->get('org_12345678');

        $this->assertOrganization($organization);
    }

    /** @test */
    public function current()
    {
        $client = new MockMollieClient([
            GetOrganizationRequest::class => MockResponse::ok('organization'),
        ]);

        /** @var Organization $organization */
        $organization = $client->organizations->current();

        $this->assertOrganization($organization);
    }

    /** @test */
    public function partner_status()
    {
        $client = new MockMollieClient([
            GetOrganizationPartnerStatusRequest::class => MockResponse::ok('partner-status'),
        ]);

        /** @var Partner $partner */
        $partner = $client->organizations->partnerStatus();

        $this->assertInstanceOf(Partner::class, $partner);
        $this->assertEquals('partner', $partner->resource);
        $this->assertNotEmpty($partner->partnerType);
        $this->assertNotEmpty($partner->partnerContractSignedAt);
        $this->assertNotEmpty($partner->_links);
    }

    protected function assertOrganization(Organization $organization)
    {
        $this->assertInstanceOf(Organization::class, $organization);
        $this->assertEquals('organization', $organization->resource);
        $this->assertNotEmpty($organization->id);
        $this->assertNotEmpty($organization->name);
        $this->assertNotEmpty($organization->email);
        $this->assertNotEmpty($organization->locale);
        $this->assertNotEmpty($organization->address);
        $this->assertNotEmpty($organization->registrationNumber);
    }
}
