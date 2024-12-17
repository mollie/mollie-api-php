<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Requests\GetOrganizationPartnerStatusRequest;
use Mollie\Api\Resources\Partner;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class OrganizationPartnerEndpointCollectionTest extends TestCase
{
    /** @test */
    public function status()
    {
        $client = new MockClient([
            GetOrganizationPartnerStatusRequest::class => new MockResponse(200, 'partner-status'),
        ]);

        /** @var Partner $partner */
        $partner = $client->organizationPartners->status();

        $this->assertInstanceOf(Partner::class, $partner);
        $this->assertEquals('partner', $partner->resource);
        $this->assertNotEmpty($partner->partnerType);
        $this->assertNotEmpty($partner->partnerContractSignedAt);
        $this->assertNotEmpty($partner->_links);
    }
}
