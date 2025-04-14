<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetOrganizationPartnerStatusRequest;
use Mollie\Api\Resources\Partner;
use PHPUnit\Framework\TestCase;

class OrganizationPartnerEndpointCollectionTest extends TestCase
{
    /** @test */
    public function status()
    {
        $client = new MockMollieClient([
            GetOrganizationPartnerStatusRequest::class => MockResponse::ok('partner-status'),
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
