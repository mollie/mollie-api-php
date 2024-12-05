<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetPermissionRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Permission;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetPermissionRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_permission()
    {
        $client = new MockClient([
            GetPermissionRequest::class => new MockResponse(200, 'permission'),
        ]);

        $request = new GetPermissionRequest('payments.read');

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());

        /** @var Permission */
        $permission = $response->toResource();

        $this->assertInstanceOf(Permission::class, $permission);
        $this->assertEquals('permission', $permission->resource);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetPermissionRequest('payments.read');

        $this->assertEquals(
            'permissions/payments.read',
            $request->resolveResourcePath()
        );
    }
}
