<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Client;
use Mollie\Api\Resources\ClientCollection;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class ClientEndpointTest extends BaseEndpointTest
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

        $client = $this->apiClient->clients->get('org_1337');

        $this->assertClient($client);
    }

    public function testGetClientsPage()
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

        $clients = $this->apiClient->clients->page();

        $this->assertInstanceOf(ClientCollection::class, $clients);
        $this->assertEquals(1, $clients->count());
        $this->assertCount(1, $clients);

        $client = $clients[0];
        $this->assertClient($client);
    }

    protected function assertClient($client)
    {
        $this->assertInstanceOf(Client::class, $client);

        $this->assertEquals('org_1337', $client->id);
        $this->assertEquals('200', $client->commission->count);
        $this->assertEquals('EUR', $client->commission->totalAmount->currency);
        $this->assertEquals('10.00', $client->commission->totalAmount->value);
        $this->assertEquals('2018-03-21T13:13:37+00:00', $client->organizationCreatedAt);

        $this->assertLinkObject(
            'https://api.mollie.com/v2/clients/org_1337',
            'application/hal+json',
            $client->_links->self
        );

        $this->assertLinkObject(
            'https://api.mollie.com/v2/organizations/org_1337',
            'application/hal+json',
            $client->_links->organization
        );

        $this->assertLinkObject(
            'https://api.mollie.com/v2/onboarding/org_1337',
            'application/hal+json',
            $client->_links->onboarding
        );

        $this->assertLinkObject(
            'https://docs.mollie.com/reference/v2/clients-api/get-client',
            'text/html',
            $client->_links->documentation
        );
    }
}
