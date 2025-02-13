<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetPermissionRequest;
use Mollie\Api\Resources\Permission;
use PHPUnit\Framework\TestCase;

class GetPermissionRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_permission()
    {
        $client = new MockMollieClient([
            GetPermissionRequest::class => MockResponse::ok('permission'),
        ]);

        $request = new GetPermissionRequest('payments.read');

        /** @var Permission */
        $permission = $client->send($request);

        $this->assertTrue($permission->getResponse()->successful());
        $this->assertInstanceOf(Permission::class, $permission);
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
