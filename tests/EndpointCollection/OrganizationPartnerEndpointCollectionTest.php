<?php

declare(strict_types=1);

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetOrganizationPartnerStatusRequest;
use Mollie\Api\Resources\Partner;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class OrganizationPartnerEndpointCollectionTest extends TestCase
{
    #[Test]
    public function it_can_get_status()
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
