<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetOrganizationPartnerStatusRequest;
use Mollie\Api\Resources\Partner;
use PHPUnit\Framework\TestCase;

class GetOrganizationPartnerStatusRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_organization_partner_status()
    {
        $client = new MockMollieClient([
            GetOrganizationPartnerStatusRequest::class => MockResponse::ok('partner-status'),
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
