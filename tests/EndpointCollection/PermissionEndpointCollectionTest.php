<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetPermissionRequest;
use Mollie\Api\Http\Requests\ListPermissionsRequest;
use Mollie\Api\Resources\Permission;
use Mollie\Api\Resources\PermissionCollection;
use PHPUnit\Framework\TestCase;

class PermissionEndpointCollectionTest extends TestCase
{
    /** @test */
    public function get()
    {
        $client = new MockMollieClient([
            GetPermissionRequest::class => MockResponse::ok('permission'),
        ]);

        /** @var Permission $permission */
        $permission = $client->permissions->get('payments.read');

        $this->assertPermission($permission);
    }

    /** @test */
    public function list()
    {
        $client = new MockMollieClient([
            ListPermissionsRequest::class => MockResponse::ok('permission-list'),
        ]);

        /** @var PermissionCollection $permissions */
        $permissions = $client->permissions->list();

        $this->assertInstanceOf(PermissionCollection::class, $permissions);
        $this->assertGreaterThan(0, $permissions->count());
        $this->assertGreaterThan(0, count($permissions));

        foreach ($permissions as $permission) {
            $this->assertPermission($permission);
        }
    }

    protected function assertPermission(Permission $permission)
    {
        $this->assertInstanceOf(Permission::class, $permission);
        $this->assertEquals('permission', $permission->resource);
        $this->assertNotEmpty($permission->id);
        $this->assertNotEmpty($permission->description);
        $this->assertIsBool($permission->granted);
        $this->assertNotEmpty($permission->_links);
    }
}
