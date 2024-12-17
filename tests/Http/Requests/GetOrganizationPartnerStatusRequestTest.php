<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetOrganizationPartnerStatusRequest;
use Mollie\Api\Resources\Partner;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class GetOrganizationPartnerStatusRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_organization_partner_status()
    {
        $client = new MockClient([
            GetOrganizationPartnerStatusRequest::class => new MockResponse(200, 'partner-status'),
        ]);

        $request = new GetOrganizationPartnerStatusRequest;

        /** @var Partner */
        $partner = $client->send($request);

        $this->assertTrue($partner->getResponse()->successful());
        $this->assertInstanceOf(Partner::class, $partner);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetOrganizationPartnerStatusRequest;

        $this->assertEquals('organizations/me/partner', $request->resolveResourcePath());
    }
}
