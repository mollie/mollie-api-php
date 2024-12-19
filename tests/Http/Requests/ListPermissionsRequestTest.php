<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\ListPermissionsRequest;
use Mollie\Api\Resources\Permission;
use Mollie\Api\Resources\PermissionCollection;
use PHPUnit\Framework\TestCase;

class ListPermissionsRequestTest extends TestCase
{
    /** @test */
    public function it_can_list_permissions()
    {
        $client = new MockMollieClient([
            ListPermissionsRequest::class => MockResponse::ok('permission-list'),
        ]);

        $request = new ListPermissionsRequest;

        /** @var PermissionCollection */
        $permissions = $client->send($request);

        $this->assertTrue($permissions->getResponse()->successful());
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
