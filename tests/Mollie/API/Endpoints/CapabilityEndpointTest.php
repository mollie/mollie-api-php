<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Capability;
use Mollie\Api\Resources\CapabilityCollection;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class CapabilityEndpointTest extends BaseEndpointTest
{
    use LinkObjectTestHelpers;

    public function testGet()
    {
        $this->mockApiCall(
            new Request('GET', '/v2/capabilities/payments'),
            new Response(
                200,
                [],
                '{
                    "resource": "capability",
                    "name": "payments",
                    "requirements": [
                        {
                            "name": "legal-representatives",
                            "dueDate": null,
                            "status": "requested"
                        }
                    ],
                    "status": "pending",
                    "statusReason": "onboarding-information-needed",
                    "organizationId": "org_12345678",
                    "_links": {
                        "self": {
                            "href": "...",
                            "type": "application/hal+json"
                        },
                        "dashboard": {
                            "href": "https://my.mollie.com/dashboard/...",
                            "type": "text/html"
                        },
                        "documentation": {
                            "href": "...",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );

        $capability = $this->apiClient->capabilities->get('payments');

        $this->assertInstanceOf(Capability::class, $capability);
    }

    public function testList()
    {
        $this->mockApiCall(
            new Request('GET', '/v2/capabilities'),
            new Response(
                200,
                [],
                '{
                    "count": 2,
                    "_embedded": {
                        "capabilities": [
                            {
                                "resource": "capability",
                                "name": "payments",
                                "requirements": [
                                    {
                                        "name": "legal-representatives",
                                        "dueDate": null,
                                        "status": "requested"
                                    },
                                    {
                                        "name": "bank-account",
                                        "dueDate": null,
                                        "status": "requested"
                                    }
                                ],
                                "status": "pending",
                                "statusReason": "onboarding-information-needed",
                                "organizationId": "org_12345678",
                                "_links": {
                                    "self": {
                                        "href": "...",
                                        "type": "application/hal+json"
                                    },
                                    "dashboard": {
                                        "href": "https://my.mollie.com/dashboard/...",
                                        "type": "text/html"
                                    }
                                }
                            },
                            {
                                "resource": "capability",
                                "name": "capital",
                                "requirements": [
                                    {
                                        "name": "legal-representatives",
                                        "dueDate": "2024-05-14T01:29:09.0Z",
                                        "status": "past-due"
                                    }
                                ],
                                "status": "disabled",
                                "statusReason": "requirement-past-due",
                                "organizationId": "org_12345678",
                                "_links": {
                                    "self": {
                                        "href": "...",
                                        "type": "application/hal+json"
                                    },
                                    "dashboard": {
                                        "href": "https://my.mollie.com/dashboard/...",
                                        "type": "text/html"
                                    }
                                }
                            }
                        ]
                    },
                    "_links": {
                        "self": {
                            "href": "...",
                            "type": "application/hal+json"
                        },
                        "documentation": {
                            "href": "...",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );

        $capabilities = $this->apiClient->capabilities->list();

        $this->assertInstanceOf(CapabilityCollection::class, $capabilities);
        $this->assertCount(2, $capabilities);
    }
}
