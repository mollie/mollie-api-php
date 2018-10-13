<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Issuer;
use Mollie\Api\Resources\IssuerCollection;
use Mollie\Api\Resources\Method;
use Mollie\Api\Resources\MethodCollection;
use stdClass;

class MethodEndpointTest extends BaseEndpointTest
{
    public function testGetMethod()
    {
        $this->mockApiCall(
            new Request('GET', '/v2/methods/ideal'),
            new Response(
                200,
                [],
                '{
                    "resource": "method",
                    "id": "ideal",
                    "description": "iDEAL",
                    "image": {
                        "size1x": "https://www.mollie.com/images/payscreen/methods/ideal.png",
                        "size2x": "https://www.mollie.com/images/payscreen/methods/ideal%402x.png"
                    },
                    "_links": {
                        "self": {
                            "href": "https://api.mollie.com/v2/methods/ideal",
                            "type": "application/hal+json"
                        },
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/methods-api/get-method",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );

        $idealMethod = $this->apiClient->methods->get('ideal');

        $this->assertInstanceOf(Method::class, $idealMethod);
        $this->assertEquals('ideal', $idealMethod->id);
        $this->assertEquals('iDEAL', $idealMethod->description);
        $this->assertEquals('https://www.mollie.com/images/payscreen/methods/ideal.png', $idealMethod->image->size1x);
        $this->assertEquals('https://www.mollie.com/images/payscreen/methods/ideal%402x.png', $idealMethod->image->size2x);

        $selfLink = (object)[
            'href' => 'https://api.mollie.com/v2/methods/ideal',
            'type' => 'application/hal+json'
        ];
        $this->assertEquals($selfLink, $idealMethod->_links->self);

        $documentationLink = (object)[
            'href' => 'https://docs.mollie.com/reference/v2/methods-api/get-method',
            'type' => 'text/html'
        ];

        $this->assertEquals($documentationLink, $idealMethod->_links->documentation);
    }

    public function testGetMethodWithIncludeIssuers()
    {
        $this->mockApiCall(
            new Request('GET', '/v2/methods/ideal?include=issuers'),
            new Response(
                200,
                [],
                '{
                    "resource": "method",
                    "id": "ideal",
                    "description": "iDEAL",
                    "image": {
                        "size1x": "https://www.mollie.com/images/payscreen/methods/ideal.png",
                        "size2x": "https://www.mollie.com/images/payscreen/methods/ideal%402x.png"
                    },
                    "issuers": [
                        {
                            "resource": "issuer",
                            "id": "ideal_TESTNL99",
                            "name": "TBM Bank",
                            "method": "ideal",
                            "image": {
                                "size1x": "https://www.mollie.com/images/checkout/v2/ideal-issuer-icons/TESTNL99.png",
                                "size2x": "https://www.mollie.com/images/checkout/v2/ideal-issuer-icons/TESTNL99.png"
                            }
                        }
                    ],

                    "_links": {
                        "self": {
                            "href": "https://api.mollie.com/v2/methods/ideal",
                            "type": "application/hal+json"
                        },
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/methods-api/get-method",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );

        $idealMethod = $this->apiClient->methods->get('ideal', ['include' => 'issuers']);

        $this->assertInstanceOf(Method::class, $idealMethod);
        $this->assertEquals('ideal', $idealMethod->id);
        $this->assertEquals('iDEAL', $idealMethod->description);
        $this->assertEquals('https://www.mollie.com/images/payscreen/methods/ideal.png', $idealMethod->image->size1x);
        $this->assertEquals('https://www.mollie.com/images/payscreen/methods/ideal%402x.png', $idealMethod->image->size2x);

        $issuers = $idealMethod->issuers();
        $this->assertInstanceOf(IssuerCollection::class, $issuers);
        $this->assertCount(1, $issuers);

        $testIssuer = $issuers[0];

        $this->assertInstanceOf(Issuer::class, $testIssuer);
        $this->assertEquals('ideal_TESTNL99', $testIssuer->id);
        $this->assertEquals('TBM Bank', $testIssuer->name);
        $this->assertEquals('ideal', $testIssuer->method);

        $expectedSize1xImageLink = 'https://www.mollie.com/images/checkout/v2/ideal-issuer-icons/TESTNL99.png';
        $this->assertEquals($expectedSize1xImageLink, $testIssuer->image->size1x);

        $expectedSize2xImageLink = 'https://www.mollie.com/images/checkout/v2/ideal-issuer-icons/TESTNL99.png';
        $this->assertEquals($expectedSize2xImageLink, $testIssuer->image->size2x);

        // TODO: self link should include query parameters.
        $selfLink = (object)[
            'href' => 'https://api.mollie.com/v2/methods/ideal',
            'type' => 'application/hal+json'
        ];
        $this->assertEquals($selfLink, $idealMethod->_links->self);

        $documentationLink = (object)[
            'href' => 'https://docs.mollie.com/reference/v2/methods-api/get-method',
            'type' => 'text/html'
        ];

        $this->assertEquals($documentationLink, $idealMethod->_links->documentation);
    }

    public function testGetTranslatedMethod()
    {
        $this->mockApiCall(
            new Request('GET', '/v2/methods/sofort?locale=de_DE'),
            new Response(
                200,
                [],
                '{
                    "resource": "method",
                    "id": "sofort",
                    "description": "SOFORT \u00dcberweisung",
                    "image": {
                        "size1x": "https://www.mollie.com/images/payscreen/methods/sofort.png",
                        "size2x": "https://www.mollie.com/images/payscreen/methods/sofort%402x.png"
                    },
                    "_links": {
                        "self": {
                            "href": "https://api.mollie.com/v2/methods/sofort",
                            "type": "application/hal+json"
                        },
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/methods-api/get-method",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );

        $method = $this->apiClient->methods->get('sofort', ['locale' => 'de_DE']);

        $this->assertInstanceOf(Method::class, $method);
        $this->assertEquals('sofort', $method->id);
        $this->assertEquals('SOFORT Überweisung', $method->description);

        $amount = new Stdclass();
        $amount->size1x = 'https://www.mollie.com/images/payscreen/methods/sofort.png';
        $amount->size2x = 'https://www.mollie.com/images/payscreen/methods/sofort%402x.png';

        $selfLink = (object)[
            'href' => 'https://api.mollie.com/v2/methods/sofort',
            'type' => 'application/hal+json'
        ];
        $this->assertEquals($selfLink, $method->_links->self);

        $documentationLink = (object)[
            'href' => 'https://docs.mollie.com/reference/v2/methods-api/get-method',
            'type' => 'text/html'
        ];

        $this->assertEquals($documentationLink, $method->_links->documentation);
    }

    public function testListMethods()
    {
        $this->mockApiCall(
            new Request('GET', '/v2/methods'),
            new Response(
                200,
                [],
                '{
                    "_embedded": {
                        "methods": [
                            {
                                "resource": "method",
                                "id": "ideal",
                                "description": "iDEAL",
                                "image": {
                                    "size1x": "https://www.mollie.com/images/payscreen/methods/ideal.png",
                                    "size2x": "https://www.mollie.com/images/payscreen/methods/ideal%402x.png"
                                },
                                "_links": {
                                    "self": {
                                        "href": "https://api.mollie.com/v2/methods/ideal",
                                        "type": "application/hal+json"
                                    }
                                }
                            },
                            {
                                "resource": "method",
                                "id": "creditcard",
                                "description": "Credit card",
                                "image": {
                                    "size1x": "https://www.mollie.com/images/payscreen/methods/creditcard.png",
                                    "size2x": "https://www.mollie.com/images/payscreen/methods/creditcard%402x.png"
                                },
                                "_links": {
                                    "self": {
                                        "href": "https://api.mollie.com/v2/methods/creditcard",
                                        "type": "application/hal+json"
                                    }
                                }
                            },
                            {
                                "resource": "method",
                                "id": "mistercash",
                                "description": "Bancontact",
                                "image": {
                                    "size1x": "https://www.mollie.com/images/payscreen/methods/mistercash.png",
                                    "size2x": "https://www.mollie.com/images/payscreen/methods/mistercash%402x.png"
                                },
                                "_links": {
                                    "self": {
                                        "href": "https://api.mollie.com/v2/methods/mistercash",
                                        "type": "application/hal+json"
                                    }
                                }
                            },
                            {
                                "resource": "method",
                                "id": "giftcard",
                                "description": "Gift cards",
                                "image": {
                                    "size1x": "https://www.mollie.com/images/payscreen/methods/giftcard.png",
                                    "size2x": "https://www.mollie.com/images/payscreen/methods/giftcard%402x.png"
                                },
                                "_links": {
                                    "self": {
                                        "href": "https://api.mollie.com/v2/methods/giftcard",
                                        "type": "application/hal+json"
                                    }
                                }
                            }
                        ]
                    },
                    "count": 4,
                    "_links": {
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/methods-api/list-methods",
                            "type": "text/html"
                        },
                        "self": {
                            "href": "http://api.mollie.com/v2/methods",
                            "type": "application/hal+json"
                        }
                    }
                }'
            )
        );

        $methods = $this->apiClient->methods->all();

        $this->assertInstanceOf(MethodCollection::class, $methods);
        $this->assertEquals(4, $methods->count);
        $this->assertCount(4, $methods);

        $documentationLink = (object)[
            'href' => 'https://docs.mollie.com/reference/v2/methods-api/list-methods',
            'type' => 'text/html'
        ];
        $this->assertEquals($documentationLink, $methods->_links->documentation);

        $selfLink = (object)[
            'href' => 'http://api.mollie.com/v2/methods',
            'type' => 'application/hal+json'
        ];
        $this->assertEquals($selfLink, $methods->_links->self);

        $creditcardMethod = $methods[1];

        $this->assertInstanceOf(Method::class, $creditcardMethod);
        $this->assertEquals('creditcard', $creditcardMethod->id);
        $this->assertEquals('Credit card', $creditcardMethod->description);
        $this->assertEquals('https://www.mollie.com/images/payscreen/methods/creditcard.png', $creditcardMethod->image->size1x);
        $this->assertEquals('https://www.mollie.com/images/payscreen/methods/creditcard%402x.png', $creditcardMethod->image->size2x);

        $selfLink = (object)[
            'href' => 'https://api.mollie.com/v2/methods/creditcard',
            'type' => 'application/hal+json'
        ];
        $this->assertEquals($selfLink, $creditcardMethod->_links->self);
    }
}
