<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Issuer;
use Mollie\Api\Resources\IssuerCollection;
use Mollie\Api\Resources\Method;
use Mollie\Api\Resources\MethodCollection;
use Mollie\Api\Resources\MethodPrice;
use Mollie\Api\Resources\MethodPriceCollection;
use stdClass;
use Tests\Mollie\TestHelpers\AmountObjectTestHelpers;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class MethodEndpointTest extends BaseEndpointTest
{
    use LinkObjectTestHelpers;
    use AmountObjectTestHelpers;

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
                    "minimumAmount": {
                        "value": "0.01",
                        "currency": "EUR"
                    },
                    "maximumAmount": {
                        "value": "50000.00",
                        "currency": "EUR"
                    },
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
        $this->assertAmountObject(0.01, 'EUR', $idealMethod->minimumAmount);
        $this->assertAmountObject(50000, 'EUR', $idealMethod->maximumAmount);
        $this->assertEquals('https://www.mollie.com/images/payscreen/methods/ideal.png', $idealMethod->image->size1x);
        $this->assertEquals('https://www.mollie.com/images/payscreen/methods/ideal%402x.png', $idealMethod->image->size2x);

        $this->assertLinkObject(
            'https://api.mollie.com/v2/methods/ideal',
            'application/hal+json',
            $idealMethod->_links->self
        );

        $this->assertLinkObject(
            'https://docs.mollie.com/reference/v2/methods-api/get-method',
            'text/html',
            $idealMethod->_links->documentation
        );
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
                    "minimumAmount": {
                        "value": "0.01",
                        "currency": "EUR"
                    },
                    "maximumAmount": {
                        "value": "50000.00",
                        "currency": "EUR"
                    },
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
        $this->assertAmountObject(0.01, 'EUR', $idealMethod->minimumAmount);
        $this->assertAmountObject(50000, 'EUR', $idealMethod->maximumAmount);
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

        $this->assertLinkObject(
            'https://api.mollie.com/v2/methods/ideal',
            'application/hal+json',
            $idealMethod->_links->self
        );

        $this->assertLinkObject(
            'https://docs.mollie.com/reference/v2/methods-api/get-method',
            'text/html',
            $idealMethod->_links->documentation
        );
    }

    public function testGetMethodWithIncludePricing()
    {
        $this->mockApiCall(
            new Request('GET', '/v2/methods/ideal?include=pricing'),
            new Response(
                200,
                [],
                '{
                     "resource": "method",
                     "id": "ideal",
                     "description": "iDEAL",
                     "minimumAmount": {
                         "value": "0.01",
                         "currency": "EUR"
                     },
                     "maximumAmount": {
                         "value": "50000.00",
                         "currency": "EUR"
                     },
                     "image": {
                         "size1x": "https://www.mollie.com/external/icons/payment-methods/ideal.png",
                         "size2x": "https://www.mollie.com/external/icons/payment-methods/ideal%402x.png",
                         "svg": "https://www.mollie.com/external/icons/payment-methods/ideal.svg"
                     },
                     "pricing": [
                         {
                             "description": "The Netherlands",
                             "fixed": {
                                 "value": "0.29",
                                 "currency": "EUR"
                             },
                             "variable": "0"
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

        $method = $this->apiClient->methods->get('ideal', ['include' => 'pricing']);

        $this->assertInstanceOf(Method::class, $method);
        $this->assertEquals('method', $method->resource);
        $this->assertEquals('ideal', $method->id);
        $this->assertEquals('iDEAL', $method->description);
        $this->assertAmountObject(0.01, 'EUR', $method->minimumAmount);
        $this->assertAmountObject(50000, 'EUR', $method->maximumAmount);
        $this->assertEquals(
            'https://www.mollie.com/external/icons/payment-methods/ideal.png',
            $method->image->size1x
        );
        $this->assertEquals(
            'https://www.mollie.com/external/icons/payment-methods/ideal%402x.png',
            $method->image->size2x
        );

        $this->assertEquals(
            'https://www.mollie.com/external/icons/payment-methods/ideal.svg',
            $method->image->svg
        );

        $this->assertLinkObject(
            'https://api.mollie.com/v2/methods/ideal',
            'application/hal+json',
            $method->_links->self
        );

        $this->assertLinkObject(
            'https://docs.mollie.com/reference/v2/methods-api/get-method',
            'text/html',
            $method->_links->documentation
        );

        $price = $method->pricing[0];

        $this->assertEquals('The Netherlands', $price->description);
        $this->assertAmountObject(0.29, 'EUR', $price->fixed);
        $this->assertEquals('0', $price->variable);

        $method_prices = $method->pricing();

        $this->assertInstanceOf(MethodPriceCollection::class, $method_prices);

        $method_price = $method_prices[0];
        $this->assertInstanceOf(MethodPrice::class, $method_price);
        $this->assertAmountObject(0.29, 'EUR', $method_price->fixed);
        $this->assertEquals('0', $method_price->variable);
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
                    "minimumAmount": {
                        "value": "0.01",
                        "currency": "EUR"
                    },
                    "maximumAmount": {
                        "value": "50000.00",
                        "currency": "EUR"
                    },
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
        $this->assertAmountObject(0.01, 'EUR', $method->minimumAmount);
        $this->assertAmountObject(50000, 'EUR', $method->maximumAmount);

        $amount = new Stdclass();
        $amount->size1x = 'https://www.mollie.com/images/payscreen/methods/sofort.png';
        $amount->size2x = 'https://www.mollie.com/images/payscreen/methods/sofort%402x.png';

        $selfLink = (object)[
            'href' => 'https://api.mollie.com/v2/methods/sofort',
            'type' => 'application/hal+json',
        ];
        $this->assertEquals($selfLink, $method->_links->self);

        $documentationLink = (object)[
            'href' => 'https://docs.mollie.com/reference/v2/methods-api/get-method',
            'type' => 'text/html',
        ];

        $this->assertEquals($documentationLink, $method->_links->documentation);
    }

    public function testListAllActiveMethods()
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
                                "minimumAmount": {
                                    "value": "0.01",
                                    "currency": "EUR"
                                },
                                "maximumAmount": {
                                    "value": "50000.00",
                                    "currency": "EUR"
                                },
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
                                "minimumAmount": {
                                    "value": "0.01",
                                    "currency": "EUR"
                                },
                                "maximumAmount": {
                                    "value": "2000.00",
                                    "currency": "EUR"
                                },
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
                                "minimumAmount": {
                                    "value": "0.02",
                                    "currency": "EUR"
                                },
                                "maximumAmount": {
                                    "value": "50000.00",
                                    "currency": "EUR"
                                },
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
                                "minimumAmount": {
                                    "value": "0.01",
                                    "currency": "EUR"
                                },
                                "maximumAmount": null,
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

        $methods = $this->apiClient->methods->allActive();

        $this->assertInstanceOf(MethodCollection::class, $methods);
        $this->assertEquals(4, $methods->count());
        $this->assertCount(4, $methods);

        $documentationLink = (object)[
            'href' => 'https://docs.mollie.com/reference/v2/methods-api/list-methods',
            'type' => 'text/html',
        ];
        $this->assertEquals($documentationLink, $methods->_links->documentation);

        $selfLink = (object)[
            'href' => 'http://api.mollie.com/v2/methods',
            'type' => 'application/hal+json',
        ];
        $this->assertEquals($selfLink, $methods->_links->self);

        $creditcardMethod = $methods[1];

        $this->assertInstanceOf(Method::class, $creditcardMethod);
        $this->assertEquals('creditcard', $creditcardMethod->id);
        $this->assertEquals('Credit card', $creditcardMethod->description);
        $this->assertAmountObject(0.01, 'EUR', $creditcardMethod->minimumAmount);
        $this->assertAmountObject(2000, 'EUR', $creditcardMethod->maximumAmount);
        $this->assertEquals('https://www.mollie.com/images/payscreen/methods/creditcard.png', $creditcardMethod->image->size1x);
        $this->assertEquals('https://www.mollie.com/images/payscreen/methods/creditcard%402x.png', $creditcardMethod->image->size2x);

        $selfLink = (object)[
            'href' => 'https://api.mollie.com/v2/methods/creditcard',
            'type' => 'application/hal+json',
        ];
        $this->assertEquals($selfLink, $creditcardMethod->_links->self);
    }

    public function testListAllAvailableMethods()
    {
        $this->mockApiCall(
            new Request('GET', '/v2/methods/all?include=pricing'),
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
                                "minimumAmount": {
                                    "value": "0.01",
                                    "currency": "EUR"
                                },
                                "maximumAmount": {
                                    "value": "50000.00",
                                    "currency": "EUR"
                                },
                                "image": {
                                    "size1x": "https://www.mollie.com/images/payscreen/methods/ideal.png",
                                    "size2x": "https://www.mollie.com/images/payscreen/methods/ideal%402x.png"
                                },
                                "pricing": [
                                    {
                                        "description": "Netherlands",
                                        "fixed": {
                                            "value": "0.29",
                                            "currency": "EUR"
                                        },
                                        "variable": "0"
                                    }
                                ],
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
                                "minimumAmount": {
                                    "value": "0.01",
                                    "currency": "EUR"
                                },
                                "maximumAmount": {
                                    "value": "2000.00",
                                    "currency": "EUR"
                                },
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
                                "minimumAmount": {
                                    "value": "0.02",
                                    "currency": "EUR"
                                },
                                "maximumAmount": {
                                    "value": "50000.00",
                                    "currency": "EUR"
                                },
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
                                "minimumAmount": {
                                    "value": "0.01",
                                    "currency": "EUR"
                                },
                                "maximumAmount": null,
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

        $methods = $this->apiClient->methods->allAvailable(['include' => 'pricing']);

        $this->assertInstanceOf(MethodCollection::class, $methods);
        $this->assertEquals(4, $methods->count());
        $this->assertCount(4, $methods);

        $this->assertLinkObject(
            'https://docs.mollie.com/reference/v2/methods-api/list-methods',
            'text/html',
            $methods->_links->documentation
        );

        $this->assertLinkObject(
            'http://api.mollie.com/v2/methods',
            'application/hal+json',
            $methods->_links->self
        );

        $creditcardMethod = $methods[1];

        $this->assertInstanceOf(Method::class, $creditcardMethod);
        $this->assertEquals('creditcard', $creditcardMethod->id);
        $this->assertEquals('Credit card', $creditcardMethod->description);
        $this->assertAmountObject(0.01, 'EUR', $creditcardMethod->minimumAmount);
        $this->assertAmountObject(2000, 'EUR', $creditcardMethod->maximumAmount);
        $this->assertEquals('https://www.mollie.com/images/payscreen/methods/creditcard.png', $creditcardMethod->image->size1x);
        $this->assertEquals('https://www.mollie.com/images/payscreen/methods/creditcard%402x.png', $creditcardMethod->image->size2x);

        $this->assertLinkObject(
            'https://api.mollie.com/v2/methods/creditcard',
            'application/hal+json',
            $creditcardMethod->_links->self
        );
    }
}
