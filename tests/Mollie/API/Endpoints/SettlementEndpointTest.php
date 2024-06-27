<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Settlement;
use Mollie\Api\Resources\SettlementCollection;
use Mollie\Api\Types\SettlementStatus;

class SettlementEndpointTest extends BaseEndpointTest
{
    public function testGetSettlement()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/settlements/stl_xcaSGAHuRt",
                [],
                ''
            ),
            new Response(
                200,
                [],
                '{
                  "resource": "settlement",
                  "id": "stl_xcaSGAHuRt",
                  "reference": "1234567.1234.12",
                  "createdAt": "2018-04-30T04:00:02+00:00",
                  "settledAt": "2018-05-01T04:00:02+00:00",
                  "status": "pending",
                  "amount": {
                    "value": "1980.98",
                    "currency": "EUR"
                  },
                  "periods": {
                    "2018": {
                      "04": {
                        "revenue": [
                            {
                              "description": "Creditcard",
                              "method": "creditcard",
                              "count": 2,
                              "amountNet": {
                                "value": "790.00",
                                "currency": "EUR"
                              },
                              "amountVat": null,
                              "amountGross": {
                                "value": "1000.00",
                                "currency": "EUR"
                              }
                            },
                            {
                              "description": "iDEAL",
                              "method": "ideal",
                              "count": 2,
                              "amountNet": {
                                "value": "790.00",
                                "currency": "EUR"
                              },
                              "amountVat": null,
                              "amountGross": {
                                "value": "1000.00",
                                "currency": "EUR"
                              }
                            }
                          ],
                          "costs": [
                            {
                              "description": "Creditcard",
                              "method": "creditcard",
                              "count": 2,
                              "rate": {
                                "fixed": {
                                  "value": "0.00",
                                  "currency": "EUR"
                                },
                                "percentage": "1.80"
                              },
                              "amountNet": {
                                "value": "14.22",
                                "currency": "EUR"
                              },
                              "amountVat": {
                                "value": "2.9862",
                                "currency": "EUR"
                              },
                              "amountGross": {
                                "value": "17.2062",
                                "currency": "EUR"
                              }
                            },
                            {
                              "description": "Fixed creditcard costs",
                              "method": "creditcard",
                              "count": 2,
                              "rate": {
                                "fixed": {
                                  "value": "0.25",
                                  "currency": "EUR"
                                },
                                "percentage": "0"
                              },
                              "amountNet": {
                                "value": "0.50",
                                "currency": "EUR"
                              },
                              "amountVat": {
                                "value": "0.105",
                                "currency": "EUR"
                              },
                              "amountGross": {
                                "value": "0.605",
                                "currency": "EUR"
                              }
                            },
                            {
                              "description": "Fixed iDEAL costs",
                              "method": "ideal",
                              "count": 2,
                              "rate": {
                                "fixed": {
                                  "value": "0.25",
                                  "currency": "EUR"
                                },
                                "percentage": "0"
                              },
                              "amountNet": {
                                "value": "0.50",
                                "currency": "EUR"
                              },
                              "amountVat": {
                                "value": "0.105",
                                "currency": "EUR"
                              },
                              "amountGross": {
                                "value": "0.605",
                                "currency": "EUR"
                              }
                            },
                            {
                              "description": "Refunds iDEAL",
                              "method": "refund",
                              "count": 2,
                              "rate": {
                                "fixed": {
                                  "value": "0.25",
                                  "currency": "EUR"
                                },
                                "percentage": "0"
                              },
                              "amountNet": {
                                "value": "0.50",
                                "currency": "EUR"
                              },
                              "amountVat": {
                                "value": "0.105",
                                "currency": "EUR"
                              },
                              "amountGross": {
                                "value": "0.605",
                                "currency": "EUR"
                              }
                            }
                          ]
                      }
                    }
                  },
                  "invoiceId": "inv_VseyTUhJSy",
                  "_links": {
                    "self": {
                      "href": "https://api.mollie.com/v2/settlements/stl_xcaSGAHuRt",
                      "type": "application/hal+json"
                    },
                    "payments": {
                      "href": "https://api.mollie.com/v2/settlements/stl_xcaSGAHuRt/payments",
                      "type": "application/hal+json"
                    },
                    "refunds": {
                      "href": "https://api.mollie.com/v2/settlements/stl_xcaSGAHuRt/refunds",
                      "type": "application/hal+json"
                    },
                    "chargebacks": {
                      "href": "https://api.mollie.com/v2/settlements/stl_xcaSGAHuRt/chargebacks",
                      "type": "application/hal+json"
                    },
                    "documentation": {
                      "href": "https://docs.mollie.com/reference/v2/settlements-api/get-settlement",
                      "type": "text/html"
                    }
                  }
                }'
            )
        );

        /** @var Settlement $settlement */
        $settlement = $this->apiClient->settlements->get("stl_xcaSGAHuRt");

        $this->assertInstanceOf(Settlement::class, $settlement);
        $this->assertEquals("settlement", $settlement->resource);
        $this->assertEquals("stl_xcaSGAHuRt", $settlement->id);
        $this->assertEquals("1234567.1234.12", $settlement->reference);
        $this->assertEquals("2018-04-30T04:00:02+00:00", $settlement->createdAt);
        $this->assertEquals("2018-05-01T04:00:02+00:00", $settlement->settledAt);
        $this->assertEquals(SettlementStatus::PENDING, $settlement->status);
        $this->assertEquals((object) ["value" => "1980.98", "currency" => "EUR"], $settlement->amount);
        $this->assertNotEmpty($settlement->periods);
        $this->assertEquals("inv_VseyTUhJSy", $settlement->invoiceId);

        $selfLink = (object)['href' => 'https://api.mollie.com/v2/settlements/stl_xcaSGAHuRt', 'type' => 'application/hal+json'];
        $this->assertEquals($selfLink, $settlement->_links->self);

        $paymentLink = (object)['href' => 'https://api.mollie.com/v2/settlements/stl_xcaSGAHuRt/payments', 'type' => 'application/hal+json'];
        $this->assertEquals($paymentLink, $settlement->_links->payments);

        $refundLink = (object)['href' => 'https://api.mollie.com/v2/settlements/stl_xcaSGAHuRt/refunds', 'type' => 'application/hal+json'];
        $this->assertEquals($refundLink, $settlement->_links->refunds);

        $chargebackLink = (object)['href' => 'https://api.mollie.com/v2/settlements/stl_xcaSGAHuRt/chargebacks', 'type' => 'application/hal+json'];
        $this->assertEquals($chargebackLink, $settlement->_links->chargebacks);

        $documentationLink = (object)['href' => 'https://docs.mollie.com/reference/v2/settlements-api/get-settlement', 'type' => 'text/html'];
        $this->assertEquals($documentationLink, $settlement->_links->documentation);
    }

    public function testListSettlement()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/settlements",
                [],
                ''
            ),
            new Response(
                200,
                [],
                '{
                  "_embedded": {
                    "settlements": [
                      {
                        "resource": "settlement",
                        "id": "stl_xcaSGAHuRt",
                        "reference": "1234567.1234.12",
                        "createdAt": "2018-04-30T04:00:02+00:00",
                        "settledAt": "2018-05-01T04:00:02+00:00",
                        "status": "pending",
                        "amount": {
                          "value": "1980.98",
                          "currency": "EUR"
                        },
                        "periods": {
                          "2018": {
                            "04": {
                              "revenue": [
                                {
                                  "description": "Creditcard",
                                  "method": "creditcard",
                                  "count": 2,
                                  "amountNet": {
                                    "value": "790.00",
                                    "currency": "EUR"
                                  },
                                  "amountVat": null,
                                  "amountGross": {
                                    "value": "1000.00",
                                    "currency": "EUR"
                                  }
                                },
                                {
                                  "description": "iDEAL",
                                  "method": "ideal",
                                  "count": 2,
                                  "amountNet": {
                                    "value": "790.00",
                                    "currency": "EUR"
                                  },
                                  "amountVat": null,
                                  "amountGross": {
                                    "value": "1000.00",
                                    "currency": "EUR"
                                  }
                                }
                              ],
                              "costs": [
                                {
                                  "description": "Creditcard",
                                  "method": "creditcard",
                                  "count": 2,
                                  "rate": {
                                    "fixed": {
                                      "value": "0.00",
                                      "currency": "EUR"
                                    },
                                    "percentage": "1.80"
                                  },
                                  "amountNet": {
                                    "value": "14.22",
                                    "currency": "EUR"
                                  },
                                  "amountVat": {
                                    "value": "2.9862",
                                    "currency": "EUR"
                                  },
                                  "amountGross": {
                                    "value": "17.2062",
                                    "currency": "EUR"
                                  }
                                },
                                {
                                  "description": "Fixed creditcard costs",
                                  "method": "creditcard",
                                  "count": 2,
                                  "rate": {
                                    "fixed": {
                                      "value": "0.25",
                                      "currency": "EUR"
                                    },
                                    "percentage": "0"
                                  },
                                  "amountNet": {
                                    "value": "0.50",
                                    "currency": "EUR"
                                  },
                                  "amountVat": {
                                    "value": "0.105",
                                    "currency": "EUR"
                                  },
                                  "amountGross": {
                                    "value": "0.605",
                                    "currency": "EUR"
                                  }
                                },
                                {
                                  "description": "Fixed iDEAL costs",
                                  "method": "ideal",
                                  "count": 2,
                                  "rate": {
                                    "fixed": {
                                      "value": "0.25",
                                      "currency": "EUR"
                                    },
                                    "percentage": "0"
                                  },
                                  "amountNet": {
                                    "value": "0.50",
                                    "currency": "EUR"
                                  },
                                  "amountVat": {
                                    "value": "0.105",
                                    "currency": "EUR"
                                  },
                                  "amountGross": {
                                    "value": "0.605",
                                    "currency": "EUR"
                                  }
                                },
                                {
                                  "description": "Refunds iDEAL",
                                  "method": "refund",
                                  "count": 2,
                                  "rate": {
                                    "fixed": {
                                      "value": "0.25",
                                      "currency": "EUR"
                                    },
                                    "percentage": "0"
                                  },
                                  "amountNet": {
                                    "value": "0.50",
                                    "currency": "EUR"
                                  },
                                  "amountVat": {
                                    "value": "0.105",
                                    "currency": "EUR"
                                  },
                                  "amountGross": {
                                    "value": "0.605",
                                    "currency": "EUR"
                                  }
                                }
                              ]
                            }
                          }
                        },
                        "_links": {
                          "self": {
                            "href": "https://api.mollie.com/v2/settlements/stl_xcaSGAHuRt",
                            "type": "application/hal+json"
                          },
                          "payments": {
                            "href": "https://api.mollie.com/v2/settlements/stl_xcaSGAHuRt/payments",
                            "type": "application/hal+json"
                          },
                          "refunds": {
                            "href": "https://api.mollie.com/v2/settlements/stl_xcaSGAHuRt/refunds",
                            "type": "application/hal+json"
                          },
                          "chargebacks": {
                            "href": "https://api.mollie.com/v2/settlements/stl_xcaSGAHuRt/chargebacks",
                            "type": "application/hal+json"
                          }
                        }
                      }
                    ]
                  },
                  "count": 1,
                  "_links": {
                    "documentation": {
                      "href": "https://docs.mollie.com/reference/v2/settlements-api/list-settlements",
                      "type": "text/html"
                    },
                    "self": {
                      "href": "https://api.mollie.nl/v2/settlements",
                      "type": "application/hal+json"
                    },
                    "previous": null,
                    "next": {
                      "href": "https://api.mollie.nl/v2/settlements?from=stl_xcaSGAHuRt&limit=1&previous=stl_xcaPACKpLs",
                      "type": "application/hal+json"
                    }
                  }
                }'
            )
        );

        /** @var SettlementCollection $settlements */
        $settlements = $this->apiClient->settlements->page();
        $this->assertInstanceOf(SettlementCollection::class, $settlements);

        $documentationLink = (object)['href' => 'https://docs.mollie.com/reference/v2/settlements-api/list-settlements', 'type' => 'text/html'];
        $this->assertEquals($documentationLink, $settlements->_links->documentation);

        $selfLink = (object)['href' => 'https://api.mollie.nl/v2/settlements', 'type' => 'application/hal+json'];
        $this->assertEquals($selfLink, $settlements->_links->self);

        $this->assertEmpty($settlements->_links->previous);

        $nextLink = (object)['href' => 'https://api.mollie.nl/v2/settlements?from=stl_xcaSGAHuRt&limit=1&previous=stl_xcaPACKpLs', 'type' => 'application/hal+json'];
        $this->assertEquals($nextLink, $settlements->_links->next);

        foreach ($settlements as $settlement) {
            $this->assertInstanceOf(Settlement::class, $settlement);
            $this->assertEquals("settlement", $settlement->resource);
            $this->assertNotEmpty($settlement->periods);
        }
    }

    public function testIterateSettlement()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/settlements",
                [],
                ''
            ),
            new Response(
                200,
                [],
                '{
                  "_embedded": {
                    "settlements": [
                      {
                        "resource": "settlement",
                        "id": "stl_xcaSGAHuRt",
                        "reference": "1234567.1234.12",
                        "createdAt": "2018-04-30T04:00:02+00:00",
                        "settledAt": "2018-05-01T04:00:02+00:00",
                        "status": "pending",
                        "amount": {
                          "value": "1980.98",
                          "currency": "EUR"
                        },
                        "periods": {
                          "2018": {
                            "04": {
                              "revenue": [
                                {
                                  "description": "Creditcard",
                                  "method": "creditcard",
                                  "count": 2,
                                  "amountNet": {
                                    "value": "790.00",
                                    "currency": "EUR"
                                  },
                                  "amountVat": null,
                                  "amountGross": {
                                    "value": "1000.00",
                                    "currency": "EUR"
                                  }
                                },
                                {
                                  "description": "iDEAL",
                                  "method": "ideal",
                                  "count": 2,
                                  "amountNet": {
                                    "value": "790.00",
                                    "currency": "EUR"
                                  },
                                  "amountVat": null,
                                  "amountGross": {
                                    "value": "1000.00",
                                    "currency": "EUR"
                                  }
                                }
                              ],
                              "costs": [
                                {
                                  "description": "Creditcard",
                                  "method": "creditcard",
                                  "count": 2,
                                  "rate": {
                                    "fixed": {
                                      "value": "0.00",
                                      "currency": "EUR"
                                    },
                                    "percentage": "1.80"
                                  },
                                  "amountNet": {
                                    "value": "14.22",
                                    "currency": "EUR"
                                  },
                                  "amountVat": {
                                    "value": "2.9862",
                                    "currency": "EUR"
                                  },
                                  "amountGross": {
                                    "value": "17.2062",
                                    "currency": "EUR"
                                  }
                                },
                                {
                                  "description": "Fixed creditcard costs",
                                  "method": "creditcard",
                                  "count": 2,
                                  "rate": {
                                    "fixed": {
                                      "value": "0.25",
                                      "currency": "EUR"
                                    },
                                    "percentage": "0"
                                  },
                                  "amountNet": {
                                    "value": "0.50",
                                    "currency": "EUR"
                                  },
                                  "amountVat": {
                                    "value": "0.105",
                                    "currency": "EUR"
                                  },
                                  "amountGross": {
                                    "value": "0.605",
                                    "currency": "EUR"
                                  }
                                },
                                {
                                  "description": "Fixed iDEAL costs",
                                  "method": "ideal",
                                  "count": 2,
                                  "rate": {
                                    "fixed": {
                                      "value": "0.25",
                                      "currency": "EUR"
                                    },
                                    "percentage": "0"
                                  },
                                  "amountNet": {
                                    "value": "0.50",
                                    "currency": "EUR"
                                  },
                                  "amountVat": {
                                    "value": "0.105",
                                    "currency": "EUR"
                                  },
                                  "amountGross": {
                                    "value": "0.605",
                                    "currency": "EUR"
                                  }
                                },
                                {
                                  "description": "Refunds iDEAL",
                                  "method": "refund",
                                  "count": 2,
                                  "rate": {
                                    "fixed": {
                                      "value": "0.25",
                                      "currency": "EUR"
                                    },
                                    "percentage": "0"
                                  },
                                  "amountNet": {
                                    "value": "0.50",
                                    "currency": "EUR"
                                  },
                                  "amountVat": {
                                    "value": "0.105",
                                    "currency": "EUR"
                                  },
                                  "amountGross": {
                                    "value": "0.605",
                                    "currency": "EUR"
                                  }
                                }
                              ]
                            }
                          }
                        },
                        "_links": {
                          "self": {
                            "href": "https://api.mollie.com/v2/settlements/stl_xcaSGAHuRt",
                            "type": "application/hal+json"
                          },
                          "payments": {
                            "href": "https://api.mollie.com/v2/settlements/stl_xcaSGAHuRt/payments",
                            "type": "application/hal+json"
                          },
                          "refunds": {
                            "href": "https://api.mollie.com/v2/settlements/stl_xcaSGAHuRt/refunds",
                            "type": "application/hal+json"
                          },
                          "chargebacks": {
                            "href": "https://api.mollie.com/v2/settlements/stl_xcaSGAHuRt/chargebacks",
                            "type": "application/hal+json"
                          }
                        }
                      }
                    ]
                  },
                  "count": 1,
                  "_links": {
                    "documentation": {
                      "href": "https://docs.mollie.com/reference/v2/settlements-api/list-settlements",
                      "type": "text/html"
                    },
                    "self": {
                      "href": "https://api.mollie.nl/v2/settlements",
                      "type": "application/hal+json"
                    },
                    "previous": null,
                    "next": null
                  }
                }'
            )
        );

        foreach ($this->apiClient->settlements->iterator() as $settlement) {
            $this->assertInstanceOf(Settlement::class, $settlement);
            $this->assertEquals("settlement", $settlement->resource);
            $this->assertNotEmpty($settlement->periods);
        }
    }
}
