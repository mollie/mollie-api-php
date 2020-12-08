<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Organization;
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
                    "locale": "nl_NL",
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
                    "locale": "nl_NL",
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

    protected function assertOrganization($organization)
    {
        $this->assertInstanceOf(Organization::class, $organization);

        $this->assertEquals('org_12345678', $organization->id);
        $this->assertEquals('Mollie B.V.', $organization->name);
        $this->assertEquals('info@mollie.com', $organization->email);
        $this->assertEquals('nl_NL', $organization->locale);

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
