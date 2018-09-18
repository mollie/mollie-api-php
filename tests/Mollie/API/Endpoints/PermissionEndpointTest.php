<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Permission;
use Mollie\Api\Resources\PermissionCollection;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class PermissionEndpointTest extends BaseEndpointTest
{
    use LinkObjectTestHelpers;

    /**
     * @param string id
     *
     * @dataProvider dpTestGetPermissionIds
     */
    public function testGetPermissionIds($permissionId)
    {
        $this->mockApiCall(
            new Request('GET', '/v2/permissions/' . $permissionId),
            new Response(
                200,
                [],
                '{
                  "resource": "permission",
                  "id": "' . $permissionId . '",
                  "description": "Some dummy permission description",
                  "granted": true,
                  "_links": {
                    "self": {
                        "href": "https://api.mollie.com/v2/permissions/' . $permissionId . '",
                        "type": "application/hal+json"
                    },
                    "documentation": {
                        "href": "https://docs.mollie.com/reference/v2/permissions-api/get-permission",
                        "type": "text/html"
                    }
                  }
                }'
            )
        );

        $permission = $this->apiClient->permissions->get($permissionId);

        $this->assertPermission($permission, $permissionId);
    }

    public function dpTestGetPermissionIds()
    {
        return [
            ['payments.read'],
            ['payments.write'],
            ['refunds.read'],
            ['refunds.write'],
            ['customers.read'],
            ['customers.write'],
            ['mandates.read'],
            ['mandates.write'],
            ['subscriptions.read'],
            ['subscriptions.write'],
            ['profiles.read'],
            ['profiles.write'],
            ['invoices.read'],
            ['invoices.write'],
            ['settlements.read'],
            ['settlements.write'],
            ['orders.read'],
            ['orders.write'],
            ['organizations.read'],
            ['organizations.write'],
        ];
    }

    protected function assertPermission($permission, $permissionId)
    {
        $this->assertInstanceOf(Permission::class, $permission);
        $this->assertEquals('permission', $permission->resource);
        $this->assertEquals($permissionId, $permission->id);
        $this->assertEquals(
            'Some dummy permission description',
            $permission->description
        );
        $this->assertTrue($permission->granted);

        $this->assertLinkObject(
            'https://api.mollie.com/v2/permissions/' . $permissionId,
            'application/hal+json',
            $permission->_links->self
        );

        $this->assertLinkObject(
            'https://docs.mollie.com/reference/v2/permissions-api/get-permission',
            'text/html',
            $permission->_links->documentation
        );
    }

    public function testListPermissions()
    {
        $this->mockApiCall(
            new Request('GET', '/v2/permissions'),
            new Response(
                200,
                [],
                '{
                    "_embedded": {
                        "permissions": [
                            {
                                "resource": "permission",
                                "id": "payments.write",
                                "description": "Some dummy permission description",
                                "granted": true,
                                "_links": {
                                    "self": {
                                        "href": "https://api.mollie.com/v2/permissions/payments.write",
                                        "type": "application/hal+json"
                                    },
                                    "documentation": {
                                        "href": "https://docs.mollie.com/reference/v2/permissions-api/get-permission",
                                        "type": "text/html"
                                    }
                                }
                            },
                            {
                                "resource": "permission",
                                "id": "payments.read",
                                "description": "Some dummy permission description",
                                "granted": true,
                                "_links": {
                                    "self": {
                                        "href": "https://api.mollie.com/v2/permissions/payments.read",
                                        "type": "application/hal+json"
                                    },
                                    "documentation": {
                                        "href": "https://docs.mollie.com/reference/v2/permissions-api/get-permission",
                                        "type": "text/html"
                                    }
                                }
                            }
                        ]
                    },
                    "count": 2,
                    "_links": {
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/permissions-api/list-permissions",
                            "type": "text/html"
                        },
                        "self": {
                            "href": "https://api.mollie.com/v2/permissions",
                            "type": "application/hal+json"
                        }
                    }
                }'
            )
        );

        $permissions = $this->apiClient->permissions->all();

        $this->assertInstanceOf(PermissionCollection::class, $permissions);

        $this->assertCount(2, $permissions);

        $this->assertPermission($permissions[0], 'payments.write');
        $this->assertPermission($permissions[1], 'payments.read');
    }
}
