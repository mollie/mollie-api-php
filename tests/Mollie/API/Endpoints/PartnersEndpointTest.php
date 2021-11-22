<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Partners;
use Mollie\Api\Resources\PartnersCollection;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class PartnersEndpointTest extends BaseEndpointTest
{
    use LinkObjectTestHelpers;

    public function testGetClient()
    {
        $this->mockApiCall(
            new Request("GET", "/v2/clients/org_1337"),
            new Response(
                200,
                [],
                '{
                    "resource": "client",
                    "id": "org_1337",
                    "organizationCreatedAt": "2018-03-21T13:13:37+00:00",
                    "commission": {
                        "count": 200,
                        "totalAmount": {
                            "currency": "EUR",
                            "value": "10.00"
                        }
                    },
                    "_links": {
                        "self": {
                            "href": "https://api.mollie.com/v2/clients/org_1337",
                            "type": "application/hal+json"
                        },
                        "organization": {
                            "href": "https://api.mollie.com/v2/organizations/org_1337",
                            "type": "application/hal+json"
                        },
                        "onboarding": {
                            "href": "https://api.mollie.com/v2/onboarding/org_1337",
                            "type": "application/hal+json"
                        },
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/clients-api/get-client",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );

        $partner = $this->apiClient->partners->get('org_1337');

        $this->assertPartner($partner);
    }

    public function testAllClients()
    {
        $this->mockApiCall(
            new Request("GET", "/v2/clients", [], ''),
            new Response(
                200,
                [],
                '{
                   "count":1,
                   "_embedded":{
                      "clients":[
                         {
                            "resource":"client",
                            "id":"org_1337",
                            "organizationCreatedAt":"2018-03-21T13:13:37+00:00",
                            "commission":{
                               "count":200,
                               "totalAmount":{
                                  "currency":"EUR",
                                  "value":"10.00"
                               }
                            },
                            "_links":{
                               "self":{
                                  "href":"https://api.mollie.com/v2/clients/org_1337",
                                  "type":"application/hal+json"
                               },
                               "organization":{
                                  "href":"https://api.mollie.com/v2/organizations/org_1337",
                                  "type":"application/hal+json"
                               },
                               "onboarding":{
                                  "href":"https://api.mollie.com/v2/onboarding/org_1337",
                                  "type":"application/hal+json"
                               },
                               "documentation":{
                                  "href":"https://docs.mollie.com/reference/v2/clients-api/get-client",
                                  "type":"text/html"
                               }
                            }
                         }
                      ]
                   },
                   "_links":{
                      "self":{
                         "href":"https://api.mollie.com/v2/clients?limit=3",
                         "type":"application/hal+json"
                      },
                      "previous":null,
                      "next":{
                         "href":"https://api.mollie.com/v2/clients?from=org_1379&limit=3",
                         "type":"application/hal+json"
                      },
                      "documentation":{
                         "href":"https://docs.mollie.com/reference/v2/clients-api/list-clients",
                         "type":"text/html"
                      }
                   }
                }'
            )
        );

        $partners = $this->apiClient->partners->page();

        $this->assertInstanceOf(PartnersCollection::class, $partners);
        $this->assertEquals(1, $partners->count);
        $this->assertCount(1, $partners);

        $partner = $partners[0];
        $this->assertPartner($partner);
    }

    protected function assertPartner($partner)
    {
        $this->assertInstanceOf(Partners::class, $partner);

        $this->assertEquals('org_1337', $partner->id);
        $this->assertEquals('200', $partner->commission->count);
        $this->assertEquals('EUR', $partner->commission->totalAmount->currency);
        $this->assertEquals('10.00', $partner->commission->totalAmount->value);
        $this->assertEquals('2018-03-21T13:13:37+00:00', $partner->organizationCreatedAt);

        $this->assertLinkObject(
            'https://api.mollie.com/v2/clients/org_1337',
            'application/hal+json',
            $partner->_links->self
        );

        $this->assertLinkObject(
            'https://api.mollie.com/v2/organizations/org_1337',
            'application/hal+json',
            $partner->_links->organization
        );

        $this->assertLinkObject(
            'https://api.mollie.com/v2/onboarding/org_1337',
            'application/hal+json',
            $partner->_links->onboarding
        );
    }
}
