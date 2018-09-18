<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Organization;
use Mollie\Api\Resources\OrganizationCollection;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class OrganizationEndpointTest extends BaseEndpointTest
{
    use LinkObjectTestHelpers;

    public function testGetOrganization()
    {
        $this->mockApiCall(
            new Request("GET", "/v2/organizations/org_12345678"),
            new Response(
                200,
                [],
                '{
                    "resource": "organization",
                    "id": "org_12345678",
                    "name": "Mollie B.V.",
                    "email": "info@mollie.com",
                    "address": {
                        "streetAndNumber": "Keizersgracht 313",
                        "postalCode": "1016 EE",
                        "city": "Amsterdam",
                        "country": "NL"
                    },
                    "registrationNumber": "30204462",
                    "vatNumber": "NL815839091B01",
                    "_links": {
                        "self": {
                            "href": "https://api.mollie.com/v2/organizations/org_12345678",
                            "type": "application/hal+json"
                        },
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/organizations-api/get-organization",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );

        $organization = $this->apiClient->organizations->get('org_12345678');

        $this->assertOrganization($organization);
    }

    public function testGetCurrentOrganization()
    {
        $this->mockApiCall(
            new Request("GET", "/v2/organizations/me"),
            new Response(
                200,
                [],
                '{
                    "resource": "organization",
                    "id": "org_12345678",
                    "name": "Mollie B.V.",
                    "email": "info@mollie.com",
                    "address": {
                        "streetAndNumber": "Keizersgracht 313",
                        "postalCode": "1016 EE",
                        "city": "Amsterdam",
                        "country": "NL"
                    },
                    "registrationNumber": "30204462",
                    "vatNumber": "NL815839091B01",
                    "_links": {
                        "self": {
                            "href": "https://api.mollie.com/v2/organizations/org_12345678",
                            "type": "application/hal+json"
                        },
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/organizations-api/get-organization",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );

        $organization = $this->apiClient->organizations->current();

        $this->assertOrganization($organization);
    }

    public function testListOrganizations()
    {
        $this->mockApiCall(
            new Request("GET", "/v2/organizations"),
            new Response(
                200,
                [],
                '{
                    "_embedded": {
                    "organizations": [
                        {
                            "resource": "organization",
                            "id": "org_12345678",
                            "name": "Mollie B.V.",
                            "email": "info@mollie.com",
                            "address": {
                                "streetAndNumber": "Keizersgracht 313",
                                "postalCode": "1016 EE",
                                "city": "Amsterdam",
                                "country": "NL"
                            },
                            "registrationNumber": "30204462",
                            "vatNumber": "NL815839091B01",
                            "_links": {
                                "self": {
                                    "href": "https://api.mollie.com/v2/organizations/org_12345678",
                                    "type": "application/hal+json"
                                },
                                "documentation": {
                                    "href": "https://docs.mollie.com/reference/v2/organizations-api/get-organization",
                                    "type": "text/html"
                                }
                            }
                        }
                    ]
                },
                "count": 1,
                "_links": {
                     "documentation": {
                         "href": "https://docs.mollie.com/reference/v2/organizations-api/list-organizations",
                         "type": "text/html"
                     },
                     "self": {
                         "href": "https://api.mollie.com/v2/organizations?limit=5",
                         "type": "application/hal+json"
                     },
                     "previous": null,
                     "next": null
                 }
                }'
            )
        );

        $organizations = $this->apiClient->organizations->page();

        $this->assertInstanceOf(OrganizationCollection::class, $organizations);
        $this->assertEquals(1, $organizations->count);

        $this->assertLinkObject(
            'https://docs.mollie.com/reference/v2/organizations-api/list-organizations',
            'text/html',
            $organizations->_links->documentation
        );

        $this->assertLinkObject(
            'https://api.mollie.com/v2/organizations?limit=5',
            'application/hal+json',
            $organizations->_links->self
        );

        $this->assertNull($organizations->_links->previous);
        $this->assertNull($organizations->_links->next);

        $this->assertOrganization($organizations[0]);
    }

    protected function assertOrganization($organization)
    {
        $this->assertInstanceOf(Organization::class, $organization);

        $this->assertEquals('org_12345678', $organization->id);
        $this->assertEquals('Mollie B.V.', $organization->name);
        $this->assertEquals('info@mollie.com', $organization->email);

        $this->assertEquals((object) [
            'streetAndNumber' => 'Keizersgracht 313',
            'postalCode' => '1016 EE',
            'city' => 'Amsterdam',
            'country' => 'NL',
        ], $organization->address);

        $this->assertEquals('30204462', $organization->registrationNumber);
        $this->assertEquals('NL815839091B01', $organization->vatNumber);

        $this->assertLinkObject(
            'https://api.mollie.com/v2/organizations/org_12345678',
            'application/hal+json',
            $organization->_links->self
        );

        $this->assertLinkObject(
            'https://docs.mollie.com/reference/v2/organizations-api/get-organization',
            'text/html',
            $organization->_links->documentation
        );
    }
}
