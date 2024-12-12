<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetOrganizationPartnerStatusRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Partner;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetOrganizationPartnerStatusRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_organization_partner_status()
    {
        $client = new MockClient([
            GetOrganizationPartnerStatusRequest::class => new MockResponse(200, 'partner-status'),
        ]);

        $request = new GetOrganizationPartnerStatusRequest;

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(Partner::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetOrganizationPartnerStatusRequest;

        $this->assertEquals('organizations/me/partner', $request->resolveResourcePath());
    }
}
