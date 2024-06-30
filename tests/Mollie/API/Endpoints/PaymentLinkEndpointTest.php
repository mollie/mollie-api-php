<?php

declare(strict_types=1);

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\PaymentLink;

class PaymentLinkEndpointTest extends BaseEndpointTest
{
    /** @test */
    public function testListPaymentLinks()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/payment-links",
                [],
                ''
            ),
            new Response(
                200,
                [],
                '{
                  "count": 1,
                  "_embedded": {
                    "payment_links": [
                      {
                        "resource": "payment-link",
                        "id": "pl_4Y0eZitmBnQ6IDoMqZQKh",
                        "mode": "live",
                        "description": "Bicycle tires",
                        "amount": {
                          "currency": "EUR",
                          "value": "24.95"
                        },
                        "archived": false,
                        "redirectUrl": "https://webshop.example.org/thanks",
                        "webhookUrl": "https://webshop.example.org/payment-links/webhook",
                        "profileId": "pfl_QkEhN94Ba",
                        "createdAt": "2021-03-20T09:29:56+00:00",
                        "expiresAt": "2023-06-06T11:00:00+00:00",
                        "_links": {
                          "self": {
                            "href": "...",
                            "type": "application/hal+json"
                          },
                          "paymentLink": {
                            "href": "https://paymentlink.mollie.com/payment/4Y0eZitmBnQ6IDoMqZQKh",
                            "type": "text/html"
                          }
                        }
                      }
                    ]
                  },
                  "_links": {
                    "self": {
                      "href": "https://api.mollie.com/v2/payment-links/",
                      "type": "application/hal+json"
                    },
                    "previous": null,
                    "next": {
                      "href": "https://api.mollie.com/v2/payment-links?from=pl_ayGNzD4TYuQtUaxNyu8aH&limit=5",
                      "type": "application/hal+json"
                    },
                    "documentation": {
                      "href": "https://docs.mollie.com/reference/list-payment-links",
                      "type": "text/html"
                    }
                  }
                }'
            )
        );

        $paymentLinks = $this->apiClient->paymentLinks->page();
        $this->assertEquals(1, $paymentLinks->count());
        $this->assertEquals('pl_4Y0eZitmBnQ6IDoMqZQKh', $paymentLinks[0]->id);
        // No need to test all attributes as these are mapped dynamically.
    }

    /** @test */
    public function testIteratePaymentLinks()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/payment-links",
                [],
                ''
            ),
            new Response(
                200,
                [],
                '{
                  "count": 1,
                  "_embedded": {
                    "payment_links": [
                      {
                        "resource": "payment-link",
                        "id": "pl_4Y0eZitmBnQ6IDoMqZQKh",
                        "mode": "live",
                        "description": "Bicycle tires",
                        "amount": {
                          "currency": "EUR",
                          "value": "24.95"
                        },
                        "archived": false,
                        "redirectUrl": "https://webshop.example.org/thanks",
                        "webhookUrl": "https://webshop.example.org/payment-links/webhook",
                        "profileId": "pfl_QkEhN94Ba",
                        "createdAt": "2021-03-20T09:29:56+00:00",
                        "expiresAt": "2023-06-06T11:00:00+00:00",
                        "_links": {
                          "self": {
                            "href": "...",
                            "type": "application/hal+json"
                          },
                          "paymentLink": {
                            "href": "https://paymentlink.mollie.com/payment/4Y0eZitmBnQ6IDoMqZQKh",
                            "type": "text/html"
                          }
                        }
                      }
                    ]
                  },
                  "_links": {
                    "self": {
                      "href": "https://api.mollie.com/v2/payment-links/",
                      "type": "application/hal+json"
                    },
                    "previous": null,
                    "next": null,
                    "documentation": {
                      "href": "https://docs.mollie.com/reference/list-payment-links",
                      "type": "text/html"
                    }
                  }
                }'
            )
        );
        foreach ($this->apiClient->paymentLinks->iterator() as $paymentLink) {
            $this->assertInstanceOf(PaymentLink::class, $paymentLink);
            $this->assertEquals("payment-link", $paymentLink->resource);
            // No need to test all attributes as these are mapped dynamically.
        }
    }

    /** @test */
    public function testGetPaymentLink()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/payment-links/pl_4Y0eZitmBnQ6IDoMqZQKh?testmode=true",
                [],
                ''
            ),
            new Response(
                200,
                [],
                '{
                  "resource": "payment-link",
                  "id": "pl_4Y0eZitmBnQ6IDoMqZQKh",
                  "mode": "live",
                  "description": "Bicycle tires",
                  "amount": {
                    "currency": "EUR",
                    "value": "24.95"
                  },
                  "archived": false,
                  "redirectUrl": "https://webshop.example.org/thanks",
                  "webhookUrl": "https://webshop.example.org/payment-links/webhook",
                  "profileId": "pfl_QkEhN94Ba",
                  "createdAt": "2021-03-20T09:29:56+00:00",
                  "expiresAt": "2023-06-06T11:00:00+00:00",
                  "_links": {
                    "self": {
                      "href": "https://api.mollie.com/v2/payment-links/pl_4Y0eZitmBnQ6IDoMqZQKh?testmode=true",
                      "type": "application/hal+json"
                    },
                    "paymentLink": {
                      "href": "https://paymentlink.mollie.com/payment/4Y0eZitmBnQ6IDoMqZQKh",
                      "type": "text/html"
                    },
                    "documentation": {
                      "href": "https://docs.mollie.com/reference/get-payment-link",
                      "type": "text/html"
                    }
                  }
                }'
            )
        );

        $paymentLink = $this->apiClient->paymentLinks->get("pl_4Y0eZitmBnQ6IDoMqZQKh", ["testmode" => true]);
        $this->assertInstanceOf(PaymentLink::class, $paymentLink);
        $this->assertEquals("payment-link", $paymentLink->resource);
        $this->assertEquals("pl_4Y0eZitmBnQ6IDoMqZQKh", $paymentLink->id);
        // No need to test all attributes as these are mapped dynamically.
    }

    /** @test */
    public function testCreatePaymentLink()
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/v2/payment-links",
                [],
                '{
                    "description": "Bicycle tires",
                    "amount": {
                        "currency": "EUR",
                        "value": "24.95"
                    },
                    "webhookUrl": "https://webshop.example.org/payment-links/webhook/",
                    "redirectUrl": "https://webshop.example.org/thanks",
                    "expiresAt": "2023-06-06T11:00:00+00:00"
                }'
            ),
            new Response(
                201,
                [],
                '{
                        "resource": "payment-link",
                        "id": "pl_4Y0eZitmBnQ6IDoMqZQKh",
                        "mode": "live",
                        "description": "Bicycle tires",
                        "amount": {
                            "currency": "EUR",
                            "value": "24.95"
                        },
                        "archived": false,
                        "redirectUrl": "https://webshop.example.org/thanks",
                        "webhookUrl": "https://webshop.example.org/payment-links/webhook",
                        "profileId": "pfl_QkEhN94Ba",
                        "createdAt": "2021-03-20T09:29:56+00:00",
                        "expiresAt": "2023-06-06T11:00:00+00:00",
                        "_links": {
                            "self": {
                              "href": "https://api.mollie.com/v2/payment-links/pl_4Y0eZitmBnQ6IDoMqZQKh?testmode=true",
                              "type": "application/hal+json"
                            },
                            "paymentLink": {
                              "href": "https://paymentlink.mollie.com/payment/4Y0eZitmBnQ6IDoMqZQKh",
                              "type": "text/html"
                            },
                            "documentation": {
                              "href": "https://docs.mollie.com/reference/create-payment-link",
                              "type": "text/html"
                            }
                        }
                    }'
            )
        );

        $paymentLink = $this->apiClient->paymentLinks->create([
            "description" => "Bicycle tires",
            "amount" => [
                "currency" => "EUR",
                "value" => "24.95",
            ],
            "webhookUrl" => "https://webshop.example.org/payment-links/webhook/",
            "redirectUrl" => "https://webshop.example.org/thanks",
            "expiresAt" => "2023-06-06T11:00:00+00:00",
        ]);

        $this->assertInstanceOf(PaymentLink::class, $paymentLink);
        $this->assertEquals('payment-link', $paymentLink->resource);
        $this->assertEquals('pl_4Y0eZitmBnQ6IDoMqZQKh', $paymentLink->id);
        // No need to test all attributes as these are mapped dynamically.
    }

    /** @test */
    public function testUpdatePaymentLink()
    {
        $this->mockApiCall(
            new Request(
                "PATCH",
                "/v2/payment-links/pl_4Y0eZitmBnQ6IDoMqZQKh",
                [],
                '{
                    "description":"Bicycle tires",
                    "archived": true
                }'
            ),
            new Response(
                200,
                [],
                '{
                  "resource": "payment-link",
                  "id": "pl_4Y0eZitmBnQ6IDoMqZQKh",
                  "mode": "live",
                  "description": "Bicycle tires",
                  "amount": {
                    "currency": "EUR",
                    "value": "24.95"
                  },
                  "archived": true,
                  "redirectUrl": "https://webshop.example.org/thanks",
                  "webhookUrl": "https://webshop.example.org/payment-links/webhook",
                  "profileId": "pfl_QkEhN94Ba",
                  "createdAt": "2021-03-20T09:29:56+00:00",
                  "expiresAt": "2023-06-06T11:00:00+00:00",
                  "_links": {
                    "self": {
                      "href": "https://api.mollie.com/v2/payment-links/pl_4Y0eZitmBnQ6IDoMqZQKh?testmode=true",
                      "type": "application/hal+json"
                    },
                    "paymentLink": {
                      "href": "https://paymentlink.mollie.com/payment/4Y0eZitmBnQ6IDoMqZQKh",
                      "type": "text/html"
                    },
                    "documentation": {
                      "href": "https://docs.mollie.com/reference/update-payment-link",
                      "type": "text/html"
                    }
                  }
                }'
            )
        );

        $paymentLink = $this->apiClient->paymentLinks->update('pl_4Y0eZitmBnQ6IDoMqZQKh', [
            'description' => 'Bicycle tires',
            'archived' => true,
        ]);

        $this->assertInstanceOf(PaymentLink::class, $paymentLink);
        $paymentLink->resource = 'payment-link';
        $paymentLink->id = 'pl_4Y0eZitmBnQ6IDoMqZQKh';
        $paymentLink->archived = true;
        // No need to test all attributes as these are mapped dynamically.
    }

    /** @test */
    public function testDeletePaymentLink()
    {
        $this->mockApiCall(
            new Request(
                "DELETE",
                "/v2/payment-links/pl_4Y0eZitmBnQ6IDoMqZQKh"
            ),
            new Response(
                204,
                [],
                null
            )
        );

        $this->apiClient->paymentLinks->delete('pl_4Y0eZitmBnQ6IDoMqZQKh');
    }
}
