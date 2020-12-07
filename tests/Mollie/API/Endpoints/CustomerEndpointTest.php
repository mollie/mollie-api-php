<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\CustomerCollection;

class CustomerEndpointTest extends BaseEndpointTest
{
    public function testCreateWorks()
    {
        $this->mockApiCall(
            new Request('POST', '/v2/customers'),
            new Response(
                200,
                [],
                '{
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
                      "href": "https://docs.mollie.com/reference/v2/customers-api/create-customer",
                      "type": "text/html"
                    }
                  }
                }'
            )
        );

        /** @var Customer $customer */
        $customer = $this->apiClient->customers->create([
            "name" => "John Doe",
            "email" => "johndoe@example.org",
        ]);

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertEquals("customer", $customer->resource);
        $this->assertEquals("cst_FhQJRw4s2n", $customer->id);
        $this->assertEquals("John Doe", $customer->name);
        $this->assertEquals("johndoe@example.org", $customer->email);
        $this->assertNull($customer->locale);
        $this->assertNull($customer->metadata);
        $this->assertEquals([], $customer->recentlyUsedMethods);
        $this->assertEquals("2018-04-19T08:49:01+00:00", $customer->createdAt);

        $documentationLink = (object)["href" => "https://docs.mollie.com/reference/v2/customers-api/create-customer", "type" => "text/html"];
        $this->assertEquals($documentationLink, $customer->_links->documentation);
    }

    public function testGetWorks()
    {
        $this->mockApiCall(
            new Request('GET', '/v2/customers/cst_FhQJRw4s2n'),
            new Response(
                200,
                [],
                '{
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
                }'
            )
        );

        /** @var Customer $customer */
        $customer = $this->apiClient->customers->get("cst_FhQJRw4s2n");

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertEquals("customer", $customer->resource);
        $this->assertEquals("cst_FhQJRw4s2n", $customer->id);
        $this->assertEquals("John Doe", $customer->name);
        $this->assertEquals("johndoe@example.org", $customer->email);
        $this->assertNull($customer->locale);
        $this->assertNull($customer->metadata);
        $this->assertEquals([], $customer->recentlyUsedMethods);
        $this->assertEquals("2018-04-19T08:49:01+00:00", $customer->createdAt);

        $documentationLink = (object)["href" => "https://docs.mollie.com/reference/v2/customers-api/get-customer", "type" => "text/html"];
        $this->assertEquals($documentationLink, $customer->_links->documentation);
    }

    public function testListWorks()
    {
        $this->mockApiCall(
            new Request('GET', '/v2/customers'),
            new Response(
                200,
                [],
                '{
                  "_embedded": {
                    "customers": [
                      {
                        "resource": "customer",
                        "id": "cst_FhQJRw4s2n",
                        "mode": "test",
                        "name": "John Doe",
                        "email": "johndoe@example.org",
                        "locale": null,
                        "metadata": null,
                        "recentlyUsedMethods": [],
                        "createdAt": "2018-04-19T08:49:01+00:00"
                      }
                    ]
                  },
                  "count": 1,
                  "_links": {
                    "documentation": {
                      "href": "https://docs.mollie.com/reference/v2/customers-api/list-customers",
                      "type": "text/html"
                    },
                    "self": {
                      "href": "https://api.mollie.com/v2/customers?limit=50",
                      "type": "application/hal+json"
                    },
                    "previous": null,
                    "next": null
                  }
                }'
            )
        );

        /** @var Customer $customer */
        $customers = $this->apiClient->customers->page();

        $this->assertInstanceOf(CustomerCollection::class, $customers);

        $documentationLink = (object)["href" => "https://docs.mollie.com/reference/v2/customers-api/list-customers", "type" => "text/html"];
        $this->assertEquals($documentationLink, $customers->_links->documentation);

        $selfLink = (object)["href" => "https://api.mollie.com/v2/customers?limit=50", "type" => "application/hal+json"];
        $this->assertEquals($selfLink, $customers->_links->self);

        foreach ($customers as $customer) {
            $this->assertInstanceOf(Customer::class, $customer);
            $this->assertEquals("customer", $customer->resource);
            $this->assertNotEmpty($customer->createdAt);
        }
    }

    public function testUpdateWorks()
    {
        $expectedName = 'Kaas Broodje';
        $expectedEmail = 'kaas.broodje@gmail.com';

        $this->mockApiCall(
            new Request('PATCH', '/v2/customers/cst_FhQJRw4s2n'),
            new Response(
                200,
                [],
                '{
                    "resource": "customer",
                    "id": "cst_FhQJRw4s2n",
                    "mode": "test",
                    "name": "' . $expectedName . '",
                    "email": "' . $expectedEmail . '",
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
                }'
            )
        );

        $customer = $this->getCustomer();
        $customer->name = $expectedName;
        $customer->email = $expectedEmail;

        $updatedCustomer = $customer->update();

        $this->assertEquals($expectedName, $updatedCustomer->name);
        $this->assertEquals($expectedEmail, $updatedCustomer->email);
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
                "self": {
                    "href": "http://api.mollie.test/v2/customers/cst_FhQJRw4s2n",
                    "type": "application/hal+json"
                },
                "documentation": {
                    "href": "https://docs.mollie.com/reference/v2/customers-api/get-customer",
                    "type": "text/html"
                }
            }
        }';

        return $this->copy(json_decode($customerJson), new Customer($this->apiClient));
    }
}
