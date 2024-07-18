<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Invoice;
use Mollie\Api\Resources\InvoiceCollection;
use Mollie\Api\Types\InvoiceStatus;

class InvoiceEndpointTest extends BaseEndpointTest
{
    public function testGetInvoice()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/invoices/inv_bsa6PvAwaK",
                [],
                ''
            ),
            new Response(
                200,
                [],
                '{
                  "resource": "invoice",
                  "id": "inv_bsa6PvAwaK",
                  "reference": "2018.190241",
                  "vatNumber": "123456789B01",
                  "status": "paid",
                  "issuedAt": "2018-05-02",
                  "paidAt": "2018-05-02",
                  "netAmount": {
                    "value": "100.00",
                    "currency": "EUR"
                  },
                  "vatAmount": {
                    "value": "0.00",
                    "currency": "EUR"
                  },
                  "grossAmount": {
                    "value": "100.00",
                    "currency": "EUR"
                  },
                  "lines": [
                    {
                      "period": "2018-04",
                      "description": "iDEAL transaction costs: april 2018",
                      "count": 1337,
                      "vatPercentage": 0,
                      "amount": {
                        "value": "50.00",
                        "currency": "EUR"
                      }
                    },
                    {
                      "period": "2018-04",
                      "description": "Refunds iDEAL: april 2018",
                      "count": 1337,
                      "vatPercentage": 0,
                      "amount": {
                        "value": "50.00",
                        "currency": "EUR"
                      }
                    }
                  ],
                  "_links": {
                    "self": {
                      "href": "https://api.mollie.com/v2/invoices/inv_bsa6PvAwaK",
                      "type": "application/hal+json"
                    },
                    "pdf": {
                      "href": "https://www.mollie.com/merchant/download/invoice/bsa6PvAwaK/79aa10f49132b7844c0243648ade6985",
                      "type": "application/pdf"
                    },
                    "documentation": {
                      "href": "https://docs.mollie.com/reference/v2/invoices-api/get-invoice",
                      "type": "text/html"
                    }
                  }
                }'
            )
        );

        $invoice = $this->apiClient->invoices->get("inv_bsa6PvAwaK");

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals("invoice", $invoice->resource);
        $this->assertEquals("inv_bsa6PvAwaK", $invoice->id);
        $this->assertEquals("2018.190241", $invoice->reference);
        $this->assertEquals("123456789B01", $invoice->vatNumber);
        $this->assertEquals(InvoiceStatus::PAID, $invoice->status);
        $this->assertEquals("2018-05-02", $invoice->issuedAt);
        $this->assertEquals("2018-05-02", $invoice->paidAt);

        $this->assertEquals((object) ["value" => "100.00", "currency" => "EUR"], $invoice->netAmount);
        $this->assertEquals((object) ["value" => "0.00", "currency" => "EUR"], $invoice->vatAmount);
        $this->assertEquals((object) ["value" => "100.00", "currency" => "EUR"], $invoice->grossAmount);

        $this->assertCount(2, $invoice->lines);

        $selfLink = (object)['href' => 'https://api.mollie.com/v2/invoices/inv_bsa6PvAwaK', 'type' => 'application/hal+json'];
        $this->assertEquals($selfLink, $invoice->_links->self);

        $pdfLink = (object)['href' => 'https://www.mollie.com/merchant/download/invoice/bsa6PvAwaK/79aa10f49132b7844c0243648ade6985', 'type' => 'application/pdf'];
        $this->assertEquals($pdfLink, $invoice->_links->pdf);

        $documentationLink = (object)['href' => 'https://docs.mollie.com/reference/v2/invoices-api/get-invoice', 'type' => 'text/html'];
        $this->assertEquals($documentationLink, $invoice->_links->documentation);
    }

    public function testListInvoices()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/invoices",
                [],
                ''
            ),
            new Response(
                200,
                [],
                '{
                  "_embedded": {
                    "invoices": [
                      {
                          "resource": "invoice",
                          "id": "inv_bsa6PvAwaK",
                          "reference": "2018.190241",
                          "vatNumber": "123456789B01",
                          "status": "paid",
                          "issuedAt": "2018-05-02",
                          "paidAt": "2018-05-02",
                          "netAmount": {
                            "value": "100.00",
                            "currency": "EUR"
                          },
                          "vatAmount": {
                            "value": "0.00",
                            "currency": "EUR"
                          },
                          "grossAmount": {
                            "value": "100.00",
                            "currency": "EUR"
                          },
                          "lines": [
                            {
                              "period": "2018-04",
                              "description": "iDEAL transaction costs: april 2018",
                              "count": 1337,
                              "vatPercentage": 0,
                              "amount": {
                                "value": "50.00",
                                "currency": "EUR"
                              }
                            },
                            {
                              "period": "2018-04",
                              "description": "Refunds iDEAL: april 2018",
                              "count": 1337,
                              "vatPercentage": 0,
                              "amount": {
                                "value": "50.00",
                                "currency": "EUR"
                              }
                            }
                          ],
                          "_links": {
                            "self": {
                              "href": "https://api.mollie.com/v2/invoices/inv_bsa6PvAwaK",
                              "type": "application/hal+json"
                            },
                            "pdf": {
                              "href": "https://www.mollie.com/merchant/download/invoice/bsa6PvAwaK/79aa10f49132b7844c0243648ade6985",
                              "type": "application/pdf"
                            },
                            "documentation": {
                              "href": "https://docs.mollie.com/reference/v2/invoices-api/get-invoice",
                              "type": "text/html"
                            }
                          }
                        }
                    ]
                  },
                  "count": 1,
                  "_links": {
                    "documentation": {
                      "href": "https://docs.mollie.com/reference/v2/invoices-api/list-invoices",
                      "type": "text/html"
                    },
                    "self": {
                      "href": "https://api.mollie.nl/v2/invoices?limit=50",
                      "type": "application/hal+json"
                    },
                    "previous": null,
                    "next": null
                  }
                }'
            )
        );

        $invoices = $this->apiClient->invoices->page();
        $this->assertInstanceOf(InvoiceCollection::class, $invoices);

        $documentationLink = (object)['href' => 'https://docs.mollie.com/reference/v2/invoices-api/list-invoices', 'type' => 'text/html'];
        $this->assertEquals($documentationLink, $invoices->_links->documentation);

        $selfLink = (object)['href' => 'https://api.mollie.nl/v2/invoices?limit=50', 'type' => 'application/hal+json'];
        $this->assertEquals($selfLink, $invoices->_links->self);

        $this->assertEmpty($invoices->_links->previous);
        $this->assertEmpty($invoices->_links->next);

        foreach ($invoices as $invoice) {
            $this->assertInstanceOf(Invoice::class, $invoice);
            $this->assertEquals("invoice", $invoice->resource);
            $this->assertNotEmpty($invoice->lines);
        }
    }

    public function testIterateInvoices()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/invoices",
                [],
                ''
            ),
            new Response(
                200,
                [],
                '{
                  "_embedded": {
                    "invoices": [
                      {
                          "resource": "invoice",
                          "id": "inv_bsa6PvAwaK",
                          "reference": "2018.190241",
                          "vatNumber": "123456789B01",
                          "status": "paid",
                          "issuedAt": "2018-05-02",
                          "paidAt": "2018-05-02",
                          "netAmount": {
                            "value": "100.00",
                            "currency": "EUR"
                          },
                          "vatAmount": {
                            "value": "0.00",
                            "currency": "EUR"
                          },
                          "grossAmount": {
                            "value": "100.00",
                            "currency": "EUR"
                          },
                          "lines": [
                            {
                              "period": "2018-04",
                              "description": "iDEAL transaction costs: april 2018",
                              "count": 1337,
                              "vatPercentage": 0,
                              "amount": {
                                "value": "50.00",
                                "currency": "EUR"
                              }
                            },
                            {
                              "period": "2018-04",
                              "description": "Refunds iDEAL: april 2018",
                              "count": 1337,
                              "vatPercentage": 0,
                              "amount": {
                                "value": "50.00",
                                "currency": "EUR"
                              }
                            }
                          ],
                          "_links": {
                            "self": {
                              "href": "https://api.mollie.com/v2/invoices/inv_bsa6PvAwaK",
                              "type": "application/hal+json"
                            },
                            "pdf": {
                              "href": "https://www.mollie.com/merchant/download/invoice/bsa6PvAwaK/79aa10f49132b7844c0243648ade6985",
                              "type": "application/pdf"
                            },
                            "documentation": {
                              "href": "https://docs.mollie.com/reference/v2/invoices-api/get-invoice",
                              "type": "text/html"
                            }
                          }
                        }
                    ]
                  },
                  "count": 1,
                  "_links": {
                    "documentation": {
                      "href": "https://docs.mollie.com/reference/v2/invoices-api/list-invoices",
                      "type": "text/html"
                    },
                    "self": {
                      "href": "https://api.mollie.nl/v2/invoices?limit=50",
                      "type": "application/hal+json"
                    },
                    "previous": null,
                    "next": null
                  }
                }'
            )
        );

        foreach ($this->apiClient->invoices->iterator() as $invoice) {
            $this->assertInstanceOf(Invoice::class, $invoice);
            $this->assertEquals("invoice", $invoice->resource);
            $this->assertNotEmpty($invoice->lines);
        }
    }
}
