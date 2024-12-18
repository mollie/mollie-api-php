<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetOrganizationRequest;
use Mollie\Api\Resources\Organization;
use PHPUnit\Framework\TestCase;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;

class GetOrganizationRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_organization()
    {
        $client = new MockMollieClient([
            GetOrganizationRequest::class => new MockResponse(200, 'organization'),
        ]);

        $organizationId = 'org_1337';
        $request = new GetOrganizationRequest($organizationId);

        /** @var Organization */
        $organization = $client->send($request);

        $this->assertTrue($organization->getResponse()->successful());
        $this->assertInstanceOf(Organization::class, $organization);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $organizationId = 'org_1337';
        $request = new GetOrganizationRequest($organizationId);

        $this->assertEquals("organizations/{$organizationId}", $request->resolveResourcePath());
    }
}
