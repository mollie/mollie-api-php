<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Types\PaymentStatus;
use Mollie\Api\Types\SequenceType;
use stdClass;

class CustomerPaymentEndpointTest extends BaseEndpointTest
{
    public function testCreateCustomerPayment()
    {
        $this->mockApiCall(
            new Request(
                "POST",
                "/v2/customers/cst_FhQJRw4s2n/payments",
                [],
                '{
                    "amount":{
                      "value":"20.00",
                      "currency":"EUR"
                    },
                    "description": "My first API payment",
                    "redirectUrl": "https://example.org/redirect",
                    "webhookUrl": "https://example.org/webhook",
                    "metadata": {
                        "order_id": "1234"
                    }
                }'
            ),
            new Response(
                201,
                [],
                '{
                   "resource":"payment",
                   "id":"tr_44aKxzEbr8",
                   "mode":"test",
                   "createdAt":"2018-03-13T14:02:29+00:00",
                   "amount":{
                      "value":"20.00",
                      "currency":"EUR"
                   },
                   "description":"My first API payment",
                   "method":null,
                   "metadata":{
                      "order_id":"1234"
                   },
                   "status":"open",
                   "isCancelable":false,
                   "expiresAt":"2018-03-13T14:17:29+00:00",
                   "details":null,
                   "profileId":"pfl_2A1gacu42V",
                   "sequenceType":"oneoff",
                   "redirectUrl":"https://example.org/redirect",
                   "webhookUrl":"https://example.org/webhook",
                   "_links":{
                      "self":{
                         "href":"https://api.mollie.com/v2/payments/tr_44aKxzEbr8",
                         "type":"application/hal+json"
                      },
                      "checkout":{
                         "href":"https://www.mollie.com/payscreen/select-method/44aKxzEbr8",
                         "type":"text/html"
                      },
                      "customer": {
                         "href": "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n",
                         "type": "application/hal+json"
                      },
                      "documentation":{
                         "href":"https://docs.mollie.com/reference/v2/customers-api/create-payment",
                         "type":"text/html"
                      }
                   }
                }'
            )
        );

        /** @var Customer $customer */
        $customer = $this->getCustomer();

        $payment = $customer->createPayment([
            "amount" => [
                "currency" => "EUR",
                "value" => "20.00",
            ],
            "description" => "My first API payment",
            "redirectUrl" => "https://example.org/redirect",
            "webhookUrl" => "https://example.org/webhook",
            "metadata" => [
                "order_id" => "1234",
            ],
        ]);

        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals('tr_44aKxzEbr8', $payment->id);
        $this->assertEquals('test', $payment->mode);
        $this->assertEquals("2018-03-13T14:02:29+00:00", $payment->createdAt);

        $amount = new Stdclass();
        $amount->value = '20.00';
        $amount->currency = "EUR";
        $this->assertEquals($amount, $payment->amount);

        $this->assertEquals('My first API payment', $payment->description);
        $this->assertNull($payment->method);
        $this->assertEquals((object)["order_id" => "1234"], $payment->metadata);
        $this->assertEquals(PaymentStatus::OPEN, $payment->status);
        $this->assertFalse($payment->isCancelable);
        $this->assertEquals("2018-03-13T14:17:29+00:00", $payment->expiresAt);
        $this->assertNull($payment->details);
        $this->assertEquals("pfl_2A1gacu42V", $payment->profileId);
        $this->assertEquals(SequenceType::ONEOFF, $payment->sequenceType);
        $this->assertEquals("https://example.org/redirect", $payment->redirectUrl);
        $this->assertEquals("https://example.org/webhook", $payment->webhookUrl);

        $selfLink = (object)["href" => "https://api.mollie.com/v2/payments/tr_44aKxzEbr8", "type" => "application/hal+json"];
        $this->assertEquals($selfLink, $payment->_links->self);

        $checkoutLink = (object)["href" => "https://www.mollie.com/payscreen/select-method/44aKxzEbr8", "type" => "text/html"];
        $this->assertEquals($checkoutLink, $payment->_links->checkout);

        $customerLink = (object)["href" => "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n", "type" => "application/hal+json"];
        $this->assertEquals($customerLink, $payment->_links->customer);

        $documentationLink = (object)["href" => "https://docs.mollie.com/reference/v2/customers-api/create-payment", "type" => "text/html"];
        $this->assertEquals($documentationLink, $payment->_links->documentation);
    }

    public function testListCustomerPayments()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/customers/cst_FhQJRw4s2n/payments?testmode=true",
                [],
                ''
            ),
            new Response(
                200,
                [],
                '{
                  "_embedded": {
                    "payments": [
                      {
                        "resource": "payment",
                        "id": "tr_admNa2tFfa",
                        "mode": "test",
                        "createdAt": "2018-03-19T15:00:50+00:00",
                        "amount": {
                          "value": "100.00",
                          "currency": "EUR"
                        },
                        "description": "Payment no 1",
                        "method": null,
                        "metadata": null,
                        "status": "open",
                        "isCancelable": false,
                        "expiresAt": "2018-03-19T15:15:50+00:00",
                        "details": null,
                        "locale": "nl_NL",
                        "profileId": "pfl_7N5qjbu42V",
                        "sequenceType": "oneoff",
                        "redirectUrl": "https://www.example.org/",
                        "_links": {
                          "self": {
                            "href": "https://api.mollie.com/v2/payments/tr_admNa2tFfa",
                            "type": "application/hal+json"
                          },
                          "checkout": {
                            "href": "https://www.mollie.com/payscreen/select-method/admNa2tFfa",
                            "type": "text/html"
                          },
                          "customer": {
                             "href": "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n",
                             "type": "application/hal+json"
                          }
                        }
                      },
                      {
                        "resource": "payment",
                        "id": "tr_bcaLc7hFfa",
                        "mode": "test",
                        "createdAt": "2018-03-19T15:00:50+00:00",
                        "amount": {
                          "value": "100.00",
                          "currency": "EUR"
                        },
                        "description": "Payment no 2",
                        "method": null,
                        "metadata": null,
                        "status": "open",
                        "isCancelable": false,
                        "expiresAt": "2018-03-19T15:15:50+00:00",
                        "details": null,
                        "locale": "nl_NL",
                        "profileId": "pfl_7N5qjbu42V",
                        "sequenceType": "oneoff",
                        "redirectUrl": "https://www.example.org/",
                        "_links": {
                          "self": {
                            "href": "https://api.mollie.com/v2/payments/tr_bcaLc7hFfa",
                            "type": "application/hal+json"
                          },
                          "checkout": {
                            "href": "https://www.mollie.com/payscreen/select-method/bcaLc7hFfa",
                            "type": "text/html"
                          },
                          "customer": {
                             "href": "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n",
                             "type": "application/hal+json"
                          }
                        }
                      },
                      {
                        "resource": "payment",
                        "id": "tr_pslHy1tFfa",
                        "mode": "test",
                        "createdAt": "2018-03-19T15:00:50+00:00",
                        "amount": {
                          "value": "100.00",
                          "currency": "EUR"
                        },
                        "description": "Payment no 3",
                        "method": null,
                        "metadata": null,
                        "status": "open",
                        "isCancelable": false,
                        "expiresAt": "2018-03-19T15:15:50+00:00",
                        "details": null,
                        "locale": "nl_NL",
                        "profileId": "pfl_7N5qjbu42V",
                        "sequenceType": "oneoff",
                        "redirectUrl": "https://www.example.org/",
                        "_links": {
                          "self": {
                            "href": "https://api.mollie.com/v2/payments/tr_pslHy1tFfa",
                            "type": "application/hal+json"
                          },
                          "checkout": {
                            "href": "https://www.mollie.com/payscreen/select-method/pslHy1tFfa",
                            "type": "text/html"
                          },
                          "customer": {
                             "href": "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n",
                             "type": "application/hal+json"
                          }
                        }
                      }
                    ]
                  },
                  "_links": {
                    "documentation": {
                      "href": "https://docs.mollie.com/reference/v2/customers-api/list-customer-payments",
                      "type": "text/html"
                    },
                    "self": {
                      "href": "https://api.mollie.com/v2/customers/cst_TkNdP8yPrH/payments?limit=50",
                      "type": "application/hal+json"
                    },
                    "previous": null,
                    "next": null
                  },
                  "count": 3
                }'
            ),
            true
        );

        /** @var Customer $customer */
        $customer = $this->getCustomer();

        $payments = $customer->payments();

        $this->assertInstanceOf(PaymentCollection::class, $payments);
        $this->assertEquals(3, $payments->count());
        $this->assertEquals(3, count($payments));

        $documentationLink = (object)["href" => "https://docs.mollie.com/reference/v2/customers-api/list-customer-payments", "type" => "text/html"];
        $this->assertEquals($documentationLink, $payments->_links->documentation);

        $selfLink = (object)["href" => "https://api.mollie.com/v2/customers/cst_TkNdP8yPrH/payments?limit=50", "type" => "application/hal+json"];
        $this->assertEquals($selfLink, $payments->_links->self);
    }

    /**
     * @return CustomerPaymentEndpointTest
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
