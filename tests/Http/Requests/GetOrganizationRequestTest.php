<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetOrganizationRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Organization;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetOrganizationRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_organization()
    {
        $client = new MockClient([
            GetOrganizationRequest::class => new MockResponse(200, 'organization'),
        ]);

        $organizationId = 'org_1337';
        $request = new GetOrganizationRequest($organizationId);

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(Organization::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $organizationId = 'org_1337';
        $request = new GetOrganizationRequest($organizationId);

        $this->assertEquals("organizations/{$organizationId}", $request->resolveResourcePath());
    }
}
