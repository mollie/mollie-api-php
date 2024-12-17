<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Requests\GetOrganizationPartnerStatusRequest;
use Mollie\Api\Http\Requests\GetOrganizationRequest;
use Mollie\Api\Resources\Organization;
use Mollie\Api\Resources\Partner;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class OrganizationEndpointCollectionTest extends TestCase
{
    /** @test */
    public function get()
    {
        $client = new MockClient([
            GetOrganizationRequest::class => new MockResponse(200, 'organization', 'org_12345678'),
        ]);

        /** @var Organization $organization */
        $organization = $client->organizations->get('org_12345678');

        $this->assertOrganization($organization);
    }

    /** @test */
    public function current()
    {
        $client = new MockClient([
            GetOrganizationRequest::class => new MockResponse(200, 'organization'),
        ]);

        /** @var Organization $organization */
        $organization = $client->organizations->current();

        $this->assertOrganization($organization);
    }

    /** @test */
    public function partner_status()
    {
        $client = new MockClient([
            GetOrganizationPartnerStatusRequest::class => new MockResponse(200, 'partner-status'),
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
