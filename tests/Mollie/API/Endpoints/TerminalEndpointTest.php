<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Terminal;
use Mollie\Api\Resources\TerminalCollection;
use Mollie\Api\Types\TerminalStatus;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class TerminalEndpointTest extends BaseEndpointTest
{
    use LinkObjectTestHelpers;

    public function testGetTerminal()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/terminals/term_7MgL4wea46qkRcoTZjWEH?testmode=true",
                [],
                ''
            ),
            new Response(
                200,
                [],
                '{
                    "id": "term_7MgL4wea46qkRcoTZjWEH",
                    "profileId": "pfl_QkEhN94Ba",
                    "status": "active",
                    "brand": "PAX",
                    "model": "A920",
                    "serialNumber": "1234567890",
                    "currency": "EUR",
                    "description": "Terminal #12345",
                    "timezone": "GMT +08:00",
                    "locale": "nl_NL",
                    "createdAt": "2022-02-12T11:58:35.0Z",
                    "updatedAt": "2022-11-15T13:32:11+00:00",
                    "activatedAt": "2022-02-12T12:13:35.0Z",
                    "_links": {
                        "self": {
                            "href": "https://api.mollie.com/v2/terminals/term_7MgL4wea46qkRcoTZjWEH",
                            "type": "application/hal+json"
                        },
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/terminals-api/get-terminal",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );

        $terminal = $this->apiClient->terminals->get("term_7MgL4wea46qkRcoTZjWEH", ["testmode" => true]);

        $this->assertInstanceOf(Terminal::class, $terminal);
        $this->assertEquals('term_7MgL4wea46qkRcoTZjWEH', $terminal->id);
        $this->assertEquals('pfl_QkEhN94Ba', $terminal->profileId);
        $this->assertEquals(TerminalStatus::ACTIVE, $terminal->status);
        $this->assertEquals('PAX', $terminal->brand);
        $this->assertEquals('A920', $terminal->model);
        $this->assertEquals('1234567890', $terminal->serialNumber);
        $this->assertEquals('EUR', $terminal->currency);
        $this->assertEquals('Terminal #12345', $terminal->description);
        $this->assertEquals('GMT +08:00', $terminal->timezone);
        $this->assertEquals('nl_NL', $terminal->locale);
        $this->assertEquals('2022-02-12T11:58:35.0Z', $terminal->createdAt);
        $this->assertEquals('2022-11-15T13:32:11+00:00', $terminal->updatedAt);
        $this->assertEquals('2022-02-12T12:13:35.0Z', $terminal->activatedAt);

        $this->assertLinkObject("https://api.mollie.com/v2/terminals/term_7MgL4wea46qkRcoTZjWEH", 'application/hal+json', $terminal->_links->self);
        $this->assertLinkObject('https://docs.mollie.com/reference/v2/terminals-api/get-terminal', 'text/html', $terminal->_links->documentation);
    }

    public function testListTerminal()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/terminals?limit=3",
                [],
                ''
            ),
            new Response(
                200,
                [],
                '{
                    "count": 3,
                    "_embedded": {
                      "terminals": [
                        {
                          "id": "term_7MgL4wea46qkRcoTZjWEH",
                          "resource": "terminal",
                          "profileId": "pfl_QkEhN94Ba",
                          "status": "active",
                          "brand": "PAX",
                          "model": "A920",
                          "serialNumber": "1234567890",
                          "currency": "EUR",
                          "description": "Terminal #12345",
                          "timezone": "GMT +08:00",
                          "locale": "nl_NL",
                          "createdAt": "2022-02-12T11:58:35.0Z",
                          "updatedAt": "2022-11-15T13:32:11+00:00",
                          "activatedAt": "2022-02-12T12:13:35.0Z",
                          "_links": {
                            "self": {
                              "href": "https://api.mollie.com/v2/terminals/term_7MgL4wea46qkRcoTZjWEH",
                              "type": "application/hal+json"
                            }
                          }
                        },
                        {
                          "id": "term_7MgL4wea46qkRcoTZjWEG",
                          "resource": "terminal",
                          "profileId": "pfl_QkEhN94Bb",
                          "status": "pending",
                          "brand": "PAX",
                          "model": "A920",
                          "serialNumber": "1234567891",
                          "currency": "EUR",
                          "description": "Terminal #12346",
                          "timezone": "GMT +08:00",
                          "locale": "nl_NL",
                          "createdAt": "2022-02-13T11:58:35.0Z",
                          "updatedAt": "2022-11-16T13:32:11+00:00",
                          "activatedAt": "2022-02-13T12:13:35.0Z",
                          "_links": {
                            "self": {
                              "href": "https://api.mollie.com/v2/terminals/term_7MgL4wea46qkRcoTZjWEG",
                              "type": "application/hal+json"
                            }
                          }
                        },
                        {
                          "id": "term_7MgL4wea46qkRcoTZjWEI",
                          "resource": "terminal",
                          "profileId": "pfl_QkEhN94Bc",
                          "status": "inactive",
                          "brand": "PAX",
                          "model": "A920",
                          "serialNumber": "1234567892",
                          "currency": "EUR",
                          "description": "Terminal #12347",
                          "timezone": "GMT +08:00",
                          "locale": "nl_NL",
                          "createdAt": "2022-02-14T11:58:35.0Z",
                          "updatedAt": "2022-11-17T13:32:11+00:00",
                          "activatedAt": "2022-02-14T12:13:35.0Z",
                          "_links": {
                            "self": {
                              "href": "https://api.mollie.com/v2/terminals/term_7MgL4wea46qkRcoTZjWEI",
                              "type": "application/hal+json"
                            }
                          }
                        }
                      ]
                    },
                    "_links": {
                      "self": {
                        "href": "https://api.mollie.com/v2/terminals?limit=3",
                        "type": "application/hal+json"
                      },
                      "previous": null,
                      "next": {
                        "href": "https://api.mollie.com/v2/terminals?from=term_7MgL4wea46qkRcoTZjWEH&limit=3",
                        "type": "application/hal+json"
                      },
                      "documentation": {
                        "href": "https://docs.mollie.com/reference/v2/terminals-api/list-terminals",
                        "type": "text/html"
                      }
                    }
                }'
            )
        );

        $terminals = $this->apiClient->terminals->page(null, 3);

        $this->assertInstanceOf(TerminalCollection::class, $terminals);
        $this->assertEquals(3, $terminals->count());
        $this->assertEquals(3, count($terminals));

        $this->assertLinkObject('https://docs.mollie.com/reference/v2/terminals-api/list-terminals', 'text/html', $terminals->_links->documentation);
        $this->assertLinkObject('https://api.mollie.com/v2/terminals?limit=3', 'application/hal+json', $terminals->_links->self);
        $this->assertLinkObject('https://api.mollie.com/v2/terminals?from=term_7MgL4wea46qkRcoTZjWEH&limit=3', 'application/hal+json', $terminals->_links->next);

        $this->assertNull($terminals->_links->previous);
    }

    public function testIterateTerminal()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/terminals",
                [],
                ''
            ),
            new Response(
                200,
                [],
                '{
                    "count": 3,
                    "_embedded": {
                      "terminals": [
                        {
                          "id": "term_7MgL4wea46qkRcoTZjWEH",
                          "resource": "terminal",
                          "profileId": "pfl_QkEhN94Ba",
                          "status": "active",
                          "brand": "PAX",
                          "model": "A920",
                          "serialNumber": "1234567890",
                          "currency": "EUR",
                          "description": "Terminal #12345",
                          "timezone": "GMT +08:00",
                          "locale": "nl_NL",
                          "createdAt": "2022-02-12T11:58:35.0Z",
                          "updatedAt": "2022-11-15T13:32:11+00:00",
                          "activatedAt": "2022-02-12T12:13:35.0Z",
                          "_links": {
                            "self": {
                              "href": "https://api.mollie.com/v2/terminals/term_7MgL4wea46qkRcoTZjWEH",
                              "type": "application/hal+json"
                            }
                          }
                        },
                        {
                          "id": "term_7MgL4wea46qkRcoTZjWEG",
                          "resource": "terminal",
                          "profileId": "pfl_QkEhN94Bb",
                          "status": "pending",
                          "brand": "PAX",
                          "model": "A920",
                          "serialNumber": "1234567891",
                          "currency": "EUR",
                          "description": "Terminal #12346",
                          "timezone": "GMT +08:00",
                          "locale": "nl_NL",
                          "createdAt": "2022-02-13T11:58:35.0Z",
                          "updatedAt": "2022-11-16T13:32:11+00:00",
                          "activatedAt": "2022-02-13T12:13:35.0Z",
                          "_links": {
                            "self": {
                              "href": "https://api.mollie.com/v2/terminals/term_7MgL4wea46qkRcoTZjWEG",
                              "type": "application/hal+json"
                            }
                          }
                        },
                        {
                          "id": "term_7MgL4wea46qkRcoTZjWEI",
                          "resource": "terminal",
                          "profileId": "pfl_QkEhN94Bc",
                          "status": "inactive",
                          "brand": "PAX",
                          "model": "A920",
                          "serialNumber": "1234567892",
                          "currency": "EUR",
                          "description": "Terminal #12347",
                          "timezone": "GMT +08:00",
                          "locale": "nl_NL",
                          "createdAt": "2022-02-14T11:58:35.0Z",
                          "updatedAt": "2022-11-17T13:32:11+00:00",
                          "activatedAt": "2022-02-14T12:13:35.0Z",
                          "_links": {
                            "self": {
                              "href": "https://api.mollie.com/v2/terminals/term_7MgL4wea46qkRcoTZjWEI",
                              "type": "application/hal+json"
                            }
                          }
                        }
                      ]
                    },
                    "_links": {
                      "self": {
                        "href": "https://api.mollie.com/v2/terminals",
                        "type": "application/hal+json"
                      },
                      "previous": null,
                      "next": null,
                      "documentation": {
                        "href": "https://docs.mollie.com/reference/v2/terminals-api/list-terminals",
                        "type": "text/html"
                      }
                    }
                }'
            )
        );

        foreach ($this->apiClient->terminals->iterator() as $terminal) {
            $this->assertInstanceOf(Terminal::class, $terminal);
            $this->assertEquals("terminal", $terminal->resource);
        }
    }
}
