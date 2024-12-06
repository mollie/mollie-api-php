<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\ListPermissionsRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Permission;
use Mollie\Api\Resources\PermissionCollection;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class ListPermissionsRequestTest extends TestCase
{
    /** @test */
    public function it_can_list_permissions()
    {
        $client = new MockClient([
            ListPermissionsRequest::class => new MockResponse(200, 'permission-list'),
        ]);

        $request = new ListPermissionsRequest;

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());

        /** @var PermissionCollection */
        $permissions = $response->toResource();

        $this->assertInstanceOf(PermissionCollection::class, $permissions);
        $this->assertGreaterThan(0, $permissions->count());

        foreach ($permissions as $permission) {
            $this->assertInstanceOf(Permission::class, $permission);
            $this->assertEquals('permission', $permission->resource);
        }
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new ListPermissionsRequest;

        $this->assertEquals('permissions', $request->resolveResourcePath());
    }
}
