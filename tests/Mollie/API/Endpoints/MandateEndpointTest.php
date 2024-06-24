<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\Mandate;
use Mollie\Api\Resources\MandateCollection;
use Mollie\Api\Types\MandateMethod;
use Mollie\Api\Types\MandateStatus;

class MandateEndpointTest extends BaseEndpointTest
{
    public function testCreateWorks()
    {
        $this->mockApiCall(
            new Request('POST', '/v2/customers/cst_FhQJRw4s2n/mandates'),
            new Response(
                200,
                [],
                '{
                  "resource": "mandate",
                  "id": "mdt_AcQl5fdL4h",
                  "status": "valid",
                  "method": "directdebit",
                  "details": {
                    "consumerName": "John Doe",
                    "consumerAccount": "NL55INGB0000000000",
                    "consumerBic": "INGBNL2A"
                  },
                  "mandateReference": null,
                  "signatureDate": "2018-05-07",
                  "createdAt": "2018-05-07T10:49:08+00:00",
                  "_links": {
                    "self": {
                      "href": "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n/mandates/mdt_AcQl5fdL4h",
                      "type": "application/hal+json"
                    },
                    "customer": {
                      "href": "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n",
                      "type": "application/hal+json"
                    },
                    "documentation": {
                      "href": "https://mollie.com/en/docs/reference/customers/create-mandate",
                      "type": "text/html"
                    }
                  }
                }'
            )
        );

        $customer = $this->getCustomer();

        /** @var Mandate $mandate */
        $mandate = $customer->createMandate([
            "consumerName" => "John Doe",
            "method" => "directdebit",
            "consumerBic" => "INGBNL2A",
            "consumerAccount" => "NL55INGB0000000000",
        ]);

        $this->assertInstanceOf(Mandate::class, $mandate);
        $this->assertEquals("mandate", $mandate->resource);
        $this->assertEquals(MandateStatus::VALID, $mandate->status);
        $this->assertEquals("directdebit", $mandate->method);
        $this->assertEquals((object) ["consumerName" => "John Doe", "consumerAccount" => "NL55INGB0000000000", "consumerBic" => "INGBNL2A"], $mandate->details);
        $this->assertNull($mandate->mandateReference);
        $this->assertEquals("2018-05-07", $mandate->signatureDate);
        $this->assertEquals("2018-05-07T10:49:08+00:00", $mandate->createdAt);

        $selfLink = (object)["href" => "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n/mandates/mdt_AcQl5fdL4h", "type" => "application/hal+json"];
        $this->assertEquals($selfLink, $mandate->_links->self);

        $customerLink = (object)["href" => "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n", "type" => "application/hal+json"];
        $this->assertEquals($customerLink, $mandate->_links->customer);

        $documentationLink = (object)["href" => "https://mollie.com/en/docs/reference/customers/create-mandate", "type" => "text/html"];
        $this->assertEquals($documentationLink, $mandate->_links->documentation);
    }

    public function testGetWorks()
    {
        $this->mockApiCall(
            new Request('GET', '/v2/customers/cst_FhQJRw4s2n/mandates/mdt_AcQl5fdL4h'),
            new Response(
                200,
                [],
                '{
                  "resource": "mandate",
                  "id": "mdt_AcQl5fdL4h",
                  "status": "valid",
                  "method": "directdebit",
                  "details": {
                    "consumerName": "John Doe",
                    "consumerAccount": "NL55INGB0000000000",
                    "consumerBic": "INGBNL2A"
                  },
                  "mandateReference": null,
                  "signatureDate": "2018-05-07",
                  "createdAt": "2018-05-07T10:49:08+00:00",
                  "_links": {
                    "self": {
                      "href": "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n/mandates/mdt_AcQl5fdL4h",
                      "type": "application/hal+json"
                    },
                    "customer": {
                      "href": "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n",
                      "type": "application/hal+json"
                    },
                    "documentation": {
                      "href": "https://mollie.com/en/docs/reference/customers/create-mandate",
                      "type": "text/html"
                    }
                  }
                }'
            )
        );

        $customer = $this->getCustomer();

        /** @var Mandate $mandate */
        $mandate = $customer->getMandate("mdt_AcQl5fdL4h");

        $this->assertInstanceOf(Mandate::class, $mandate);
        $this->assertEquals("mandate", $mandate->resource);
        $this->assertEquals(MandateStatus::VALID, $mandate->status);
        $this->assertEquals(MandateMethod::DIRECTDEBIT, $mandate->method);
        $this->assertEquals((object) ["consumerName" => "John Doe", "consumerAccount" => "NL55INGB0000000000", "consumerBic" => "INGBNL2A"], $mandate->details);
        $this->assertNull($mandate->mandateReference);
        $this->assertEquals("2018-05-07", $mandate->signatureDate);
        $this->assertEquals("2018-05-07T10:49:08+00:00", $mandate->createdAt);

        $selfLink = (object)["href" => "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n/mandates/mdt_AcQl5fdL4h", "type" => "application/hal+json"];
        $this->assertEquals($selfLink, $mandate->_links->self);

        $customerLink = (object)["href" => "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n", "type" => "application/hal+json"];
        $this->assertEquals($customerLink, $mandate->_links->customer);

        $documentationLink = (object)["href" => "https://mollie.com/en/docs/reference/customers/create-mandate", "type" => "text/html"];
        $this->assertEquals($documentationLink, $mandate->_links->documentation);
    }

    public function testListWorks()
    {
        $this->mockApiCall(
            new Request('GET', '/v2/customers/cst_FhQJRw4s2n/mandates'),
            new Response(
                200,
                [],
                '{
                  "_embedded": {
                    "mandates": [
                      {
                        "resource": "mandate",
                        "id": "mdt_AcQl5fdL4h",
                        "status": "valid",
                        "method": "directdebit",
                        "details": {
                          "consumerName": "John Doe",
                          "consumerAccount": "NL55INGB0000000000",
                          "consumerBic": "INGBNL2A"
                        },
                        "mandateReference": null,
                        "signatureDate": "2018-05-07",
                        "createdAt": "2018-05-07T10:49:08+00:00",
                        "_links": {
                          "self": {
                            "href": "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n/mandates/mdt_AcQl5fdL4h",
                            "type": "application/hal+json"
                          },
                          "customer": {
                            "href": "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n",
                            "type": "application/hal+json"
                          }
                        }
                      }
                    ]
                  },
                  "count": 1,
                  "_links": {
                    "documentation": {
                      "href": "https://mollie.com/en/docs/reference/customers/list-mandates",
                      "type": "text/html"
                    },
                    "self": {
                      "href": "https://api.mollie.com/v2/customers/cst_vzEExMcxj7/mandates?limit=50",
                      "type": "application/hal+json"
                    },
                    "previous": null,
                    "next": null
                  }
                }'
            )
        );

        $customer = $this->getCustomer();

        /** @var MandateCollection $mandates */
        $mandates = $customer->mandates();
        $this->assertInstanceOf(MandateCollection::class, $mandates);

        /** @var Mandate $mandate */
        foreach ($mandates as $mandate) {
            $this->assertInstanceOf(Mandate::class, $mandate);
            $this->assertEquals("mandate", $mandate->resource);
            $this->assertEquals(MandateStatus::VALID, $mandate->status);

            $customerLink = (object)["href" => "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n", "type" => "application/hal+json"];
            $this->assertEquals($customerLink, $mandate->_links->customer);
        }

        $selfLink = (object)["href" => "https://api.mollie.com/v2/customers/cst_vzEExMcxj7/mandates?limit=50", "type" => "application/hal+json"];
        $this->assertEquals($selfLink, $mandates->_links->self);

        $documentationLink = (object)["href" => "https://mollie.com/en/docs/reference/customers/list-mandates", "type" => "text/html"];
        $this->assertEquals($documentationLink, $mandates->_links->documentation);
    }

    public function testCustomerHasValidMandateWhenTrue()
    {
        $this->mockApiCall(
            new Request('GET', '/v2/customers/cst_FhQJRw4s2n/mandates'),
            new Response(
                200,
                [],
                '{
                  "_embedded": {
                    "mandates": [
                      {
                        "resource": "mandate",
                        "id": "mdt_AcQl5fdL4h",
                        "status": "valid",
                        "method": "directdebit",
                        "details": {
                          "consumerName": "John Doe",
                          "consumerAccount": "NL55INGB0000000000",
                          "consumerBic": "INGBNL2A"
                        },
                        "mandateReference": null,
                        "signatureDate": "2018-05-07",
                        "createdAt": "2018-05-07T10:49:08+00:00",
                        "_links": {
                          "self": {
                            "href": "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n/mandates/mdt_AcQl5fdL4h",
                            "type": "application/hal+json"
                          },
                          "customer": {
                            "href": "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n",
                            "type": "application/hal+json"
                          }
                        }
                      }
                    ]
                  },
                  "count": 1,
                  "_links": {
                    "documentation": {
                      "href": "https://mollie.com/en/docs/reference/customers/list-mandates",
                      "type": "text/html"
                    },
                    "self": {
                      "href": "https://api.mollie.com/v2/customers/cst_vzEExMcxj7/mandates?limit=50",
                      "type": "application/hal+json"
                    },
                    "previous": null,
                    "next": null
                  }
                }'
            )
        );

        $customer = $this->getCustomer();

        $this->assertTrue($customer->hasValidMandate());
    }

    public function testCustomerHasValidMandateWhenFalse()
    {
        $this->mockApiCall(
            new Request('GET', '/v2/customers/cst_FhQJRw4s2n/mandates'),
            new Response(
                200,
                [],
                '{
                  "_embedded": {
                    "mandates": []
                  },
                  "count": 0,
                  "_links": {
                    "documentation": {
                      "href": "https://mollie.com/en/docs/reference/customers/list-mandates",
                      "type": "text/html"
                    },
                    "self": {
                      "href": "https://api.mollie.com/v2/customers/cst_vzEExMcxj7/mandates?limit=50",
                      "type": "application/hal+json"
                    },
                    "previous": null,
                    "next": null
                  }
                }'
            )
        );

        $customer = $this->getCustomer();

        $this->assertFalse($customer->hasValidMandate());
    }

    public function testCustomerHasValidMandateForMethodWhenFalse()
    {
        $this->mockApiCall(
            new Request('GET', '/v2/customers/cst_FhQJRw4s2n/mandates'),
            new Response(
                200,
                [],
                '{
                  "_embedded": {
                    "mandates": []
                  },
                  "count": 0,
                  "_links": {
                    "documentation": {
                      "href": "https://mollie.com/en/docs/reference/customers/list-mandates",
                      "type": "text/html"
                    },
                    "self": {
                      "href": "https://api.mollie.com/v2/customers/cst_vzEExMcxj7/mandates?limit=50",
                      "type": "application/hal+json"
                    },
                    "previous": null,
                    "next": null
                  }
                }'
            )
        );

        $customer = $this->getCustomer();

        $this->assertFalse($customer->hasValidMandateForMethod('directdebit'));
    }

    public function testCustomerHasValidMandateForMethodWhenTrue()
    {
        $this->mockApiCall(
            new Request('GET', '/v2/customers/cst_FhQJRw4s2n/mandates'),
            new Response(
                200,
                [],
                '{
                  "_embedded": {
                    "mandates": [
                      {
                        "resource": "mandate",
                        "id": "mdt_AcQl5fdL4h",
                        "status": "valid",
                        "method": "directdebit",
                        "details": {
                          "consumerName": "John Doe",
                          "consumerAccount": "NL55INGB0000000000",
                          "consumerBic": "INGBNL2A"
                        },
                        "mandateReference": null,
                        "signatureDate": "2018-05-07",
                        "createdAt": "2018-05-07T10:49:08+00:00",
                        "_links": {
                          "self": {
                            "href": "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n/mandates/mdt_AcQl5fdL4h",
                            "type": "application/hal+json"
                          },
                          "customer": {
                            "href": "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n",
                            "type": "application/hal+json"
                          }
                        }
                      }
                    ]
                  },
                  "count": 1,
                  "_links": {
                    "documentation": {
                      "href": "https://mollie.com/en/docs/reference/customers/list-mandates",
                      "type": "text/html"
                    },
                    "self": {
                      "href": "https://api.mollie.com/v2/customers/cst_vzEExMcxj7/mandates?limit=50",
                      "type": "application/hal+json"
                    },
                    "previous": null,
                    "next": null
                  }
                }'
            )
        );

        $customer = $this->getCustomer();

        $this->assertTrue($customer->hasValidMandateForMethod('directdebit'));
    }

    /**
     * @return Customer
     */
    private function getCustomer()
    {
        $customerJson = '{
          "resource": "customer",
          "id": "cst_FhQJRw4s2n",
          "mode": "test",
          "name": "John Doe",
          "email": "johndoe@example.org",
          "locale": null,
          "metadata": null,
          "recentlyUsedMethods": [],
          "createdAt": "2018-04-19T08:49:01+00:00",
          "_links": {
            "documentation": {
              "href": "https://docs.mollie.com/reference/v2/customers-api/get-customer",
              "type": "text/html"
            }
          }
        }';

        return $this->copy(json_decode($customerJson), new Customer($this->apiClient));
    }
}
