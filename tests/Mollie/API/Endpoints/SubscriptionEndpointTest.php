<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\Subscription;
use Mollie\Api\Resources\SubscriptionCollection;
use Mollie\Api\Types\SubscriptionStatus;

class SubscriptionEndpointTest extends BaseEndpointTest
{
    public function testCreateWorks()
    {
        $this->mockApiCall(
            new Request('POST', '/v2/customers/cst_FhQJRw4s2n/subscriptions'),
            new Response(
                200,
                [],
                '{
                    "resource": "subscription",
                    "id": "sub_wByQa6efm6",
                    "mode": "test",
                    "createdAt": "2018-04-24T11:41:55+00:00",
                    "status": "active",
                    "amount": {
                        "value": "10.00",
                        "currency": "EUR"
                    },
                    "description": "Order 1234",
                    "method": null,
                    "times": null,
                    "interval": "1 month",
                    "startDate": "2018-04-24",
                    "webhookUrl": null,
                    "_links": {
                        "self": {
                            "href": "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n/subscriptions/sub_wByQa6efm6",
                            "type": "application/hal+json"
                        },
                        "customer": {
                            "href": "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n",
                            "type": "application/hal+json"
                        },
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/subscriptions-api/create-subscription",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );

        $customer = $this->getCustomer();

        /** @var Subscription $subscription */
        $subscription = $customer->createSubscription([
            "amount" => [
                "value" => "10.00",
                "currency" => "EUR",
            ],
            "interval" => "1 month",
            "description" => "Order 1234",
        ]);

        $this->assertInstanceOf(Subscription::class, $subscription);
        $this->assertEquals("subscription", $subscription->resource);
        $this->assertEquals("sub_wByQa6efm6", $subscription->id);
        $this->assertEquals("test", $subscription->mode);
        $this->assertEquals("2018-04-24T11:41:55+00:00", $subscription->createdAt);
        $this->assertEquals(SubscriptionStatus::ACTIVE, $subscription->status);
        $this->assertEquals((object)["value" => "10.00", "currency" => "EUR"], $subscription->amount);
        $this->assertEquals("Order 1234", $subscription->description);
        $this->assertNull($subscription->method);
        $this->assertNull($subscription->times);
        $this->assertEquals("1 month", $subscription->interval);
        $this->assertEquals("2018-04-24", $subscription->startDate);

        $selfLink = (object)["href" => "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n/subscriptions/sub_wByQa6efm6", "type" => "application/hal+json"];
        $this->assertEquals($selfLink, $subscription->_links->self);

        $customerLink = (object)["href" => "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n", "type" => "application/hal+json"];
        $this->assertEquals($customerLink, $subscription->_links->customer);

        $documentationLink = (object)["href" => "https://docs.mollie.com/reference/v2/subscriptions-api/create-subscription", "type" => "text/html"];
        $this->assertEquals($documentationLink, $subscription->_links->documentation);
    }

    public function testGetWorks()
    {
        $this->mockApiCall(
            new Request('GET', '/v2/customers/cst_FhQJRw4s2n/subscriptions/sub_wByQa6efm6'),
            new Response(
                200,
                [],
                '{
                    "resource": "subscription",
                    "id": "sub_wByQa6efm6",
                    "mode": "test",
                    "createdAt": "2018-04-24T11:41:55+00:00",
                    "status": "active",
                    "amount": {
                        "value": "10.00",
                        "currency": "EUR"
                    },
                    "description": "Order 1234",
                    "method": null,
                    "times": null,
                    "interval": "1 month",
                    "startDate": "2018-04-24",
                    "webhookUrl": null,
                    "_links": {
                        "self": {
                            "href": "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n/subscriptions/sub_wByQa6efm6",
                            "type": "application/hal+json"
                        },
                        "customer": {
                            "href": "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n",
                            "type": "application/hal+json"
                        },
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/subscriptions-api/get-subscription",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );

        $customer = $this->getCustomer();

        /** @var Subscription $subscription */
        $subscription = $customer->getSubscription("sub_wByQa6efm6");

        $this->assertInstanceOf(Subscription::class, $subscription);
        $this->assertEquals("subscription", $subscription->resource);
        $this->assertEquals("sub_wByQa6efm6", $subscription->id);
        $this->assertEquals("test", $subscription->mode);
        $this->assertEquals("2018-04-24T11:41:55+00:00", $subscription->createdAt);
        $this->assertEquals(SubscriptionStatus::ACTIVE, $subscription->status);
        $this->assertEquals((object)["value" => "10.00", "currency" => "EUR"], $subscription->amount);
        $this->assertEquals("Order 1234", $subscription->description);
        $this->assertNull($subscription->method);
        $this->assertNull($subscription->times);
        $this->assertEquals("1 month", $subscription->interval);
        $this->assertEquals("2018-04-24", $subscription->startDate);

        $selfLink = (object)["href" => "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n/subscriptions/sub_wByQa6efm6", "type" => "application/hal+json"];
        $this->assertEquals($selfLink, $subscription->_links->self);

        $customerLink = (object)["href" => "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n", "type" => "application/hal+json"];
        $this->assertEquals($customerLink, $subscription->_links->customer);

        $documentationLink = (object)["href" => "https://docs.mollie.com/reference/v2/subscriptions-api/get-subscription", "type" => "text/html"];
        $this->assertEquals($documentationLink, $subscription->_links->documentation);
    }

    public function testListWorks()
    {
        $this->mockApiCall(
            new Request('GET', '/v2/customers/cst_FhQJRw4s2n/subscriptions'),
            new Response(
                200,
                [],
                '{
                    "_embedded": {
                        "subscriptions": [{
                            "resource": "subscription",
                            "id": "sub_wByQa6efm6",
                            "mode": "test",
                            "createdAt": "2018-04-24T11:41:55+00:00",
                            "status": "active",
                            "amount": {
                                "value": "10.00",
                                "currency": "EUR"
                            },
                            "description": "Order 1234",
                            "method": null,
                            "times": null,
                            "interval": "1 month",
                            "startDate": "2018-04-24",
                            "webhookUrl": null,
                            "_links": {
                                "self": {
                                    "href": "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n/subscriptions/sub_wByQa6efm6",
                                    "type": "application/hal+json"
                                },
                                "customer": {
                                    "href": "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n",
                                    "type": "application/hal+json"
                                }
                            }
                        }]
                    },
                    "count": 1,
                    "_links": {
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/subscriptions-api/list-subscriptions",
                            "type": "text/html"
                        },
                        "self": {
                            "href": "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n/subscriptions?limit=50",
                            "type": "application/hal+json"
                        },
                        "previous": null,
                        "next": null
                    }
                }'
            )
        );

        $customer = $this->getCustomer();

        $subscriptions = $customer->subscriptions();

        $this->assertInstanceOf(SubscriptionCollection::class, $subscriptions);

        $this->assertEquals(count($subscriptions), $subscriptions->count());

        $documentationLink = (object)["href" => "https://docs.mollie.com/reference/v2/subscriptions-api/list-subscriptions", "type" => "text/html"];
        $this->assertEquals($documentationLink, $subscriptions->_links->documentation);

        $selfLink = (object)["href" => "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n/subscriptions?limit=50", "type" => "application/hal+json"];
        $this->assertEquals($selfLink, $subscriptions->_links->self);

        foreach ($subscriptions as $subscription) {
            $this->assertInstanceOf(Subscription::class, $subscription);
            $this->assertEquals("subscription", $subscription->resource);
            $this->assertNotEmpty($subscription->createdAt);
        }
    }

    public function testCancelViaCustomerResourceWorks()
    {
        $this->mockApiCall(
            new Request('DELETE', '/v2/customers/cst_FhQJRw4s2n/subscriptions/sub_wByQa6efm6'),
            new Response(
                200,
                [],
                '{
                    "resource": "subscription",
                    "id": "sub_wByQa6efm6",
                    "mode": "test",
                    "createdAt": "2018-04-24T11:41:55+00:00",
                    "status": "canceled",
                    "amount": {
                        "value": "10.00",
                        "currency": "EUR"
                    },
                    "description": "Order 1234",
                    "method": null,
                    "times": null,
                    "interval": "1 month",
                    "startDate": "2018-04-24",
                    "webhookUrl": null,
                    "canceledAt": "2018-04-24T12:31:32+00:00",
                    "_links": {
                        "self": {
                            "href": "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n/subscriptions/sub_wByQa6efm6",
                            "type": "application/hal+json"
                        },
                        "customer": {
                            "href": "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n",
                            "type": "application/hal+json"
                        },
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/subscriptions-api/cancel-subscription",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );

        $customer = $this->getCustomer();

        /** @var Subscription $subscription */
        $subscription = $customer->cancelSubscription("sub_wByQa6efm6");

        $this->assertInstanceOf(Subscription::class, $subscription);
        $this->assertEquals("subscription", $subscription->resource);
        $this->assertEquals("sub_wByQa6efm6", $subscription->id);
        $this->assertEquals("test", $subscription->mode);
        $this->assertEquals(SubscriptionStatus::CANCELED, $subscription->status);
        $this->assertEquals("2018-04-24T11:41:55+00:00", $subscription->createdAt);
        $this->assertEquals("2018-04-24T12:31:32+00:00", $subscription->canceledAt);


        $selfLink = (object)["href" => "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n/subscriptions/sub_wByQa6efm6", "type" => "application/hal+json"];
        $this->assertEquals($selfLink, $subscription->_links->self);

        $customerLink = (object)["href" => "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n", "type" => "application/hal+json"];
        $this->assertEquals($customerLink, $subscription->_links->customer);

        $documentationLink = (object)["href" => "https://docs.mollie.com/reference/v2/subscriptions-api/cancel-subscription", "type" => "text/html"];
        $this->assertEquals($documentationLink, $subscription->_links->documentation);
    }

    public function testCancelOnSubscriptionResourceWorks($value = '')
    {
        $this->mockApiCall(
            new Request('DELETE', '/v2/customers/cst_VhjQebNW5j/subscriptions/sub_DRjwaT5qHx'),
            new Response(
                200,
                [],
                '{
                    "resource": "subscription",
                    "id": "sub_DRjwaT5qHx",
                    "mode": "test",
                    "createdAt": "2018-04-24T11:41:55+00:00",
                    "status": "canceled",
                    "amount": {
                        "value": "10.00",
                        "currency": "EUR"
                    },
                    "description": "Order 1234",
                    "method": null,
                    "times": null,
                    "interval": "1 month",
                    "startDate": "2018-04-24",
                    "webhookUrl": null,
                    "canceledAt": "2018-04-24T12:31:32+00:00",
                    "_links": {
                        "self": {
                            "href": "https://api.mollie.com/v2/customers/cst_VhjQebNW5j/subscriptions/sub_DRjwaT5qHx",
                            "type": "application/hal+json"
                        },
                        "customer": {
                            "href": "https://api.mollie.com/v2/customers/cst_VhjQebNW5j",
                            "type": "application/hal+json"
                        },
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/subscriptions-api/cancel-subscription",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );

        $subscription = $this->getSubscription();

        $subscription = $subscription->cancel();

        $this->assertInstanceOf(Subscription::class, $subscription);
        $this->assertEquals("subscription", $subscription->resource);
        $this->assertEquals("sub_DRjwaT5qHx", $subscription->id);
        $this->assertEquals("test", $subscription->mode);
        $this->assertEquals(SubscriptionStatus::CANCELED, $subscription->status);
        $this->assertEquals("2018-04-24T11:41:55+00:00", $subscription->createdAt);
        $this->assertEquals("2018-04-24T12:31:32+00:00", $subscription->canceledAt);


        $selfLink = (object)["href" => "https://api.mollie.com/v2/customers/cst_VhjQebNW5j/subscriptions/sub_DRjwaT5qHx", "type" => "application/hal+json"];
        $this->assertEquals($selfLink, $subscription->_links->self);

        $customerLink = (object)["href" => "https://api.mollie.com/v2/customers/cst_VhjQebNW5j", "type" => "application/hal+json"];
        $this->assertEquals($customerLink, $subscription->_links->customer);

        $documentationLink = (object)["href" => "https://docs.mollie.com/reference/v2/subscriptions-api/cancel-subscription", "type" => "text/html"];
        $this->assertEquals($documentationLink, $subscription->_links->documentation);
    }

    public function testThatUpdateSubscriptionWorks()
    {
        $expectedAmountValue = '12.00';
        $expectedAmountCurrency = 'EUR';
        $expectedStartDate = '2018-12-12';

        $this->mockApiCall(
            new Request('PATCH', '/v2/customers/cst_VhjQebNW5j/subscriptions/sub_DRjwaT5qHx'),
            new Response(
                200,
                [],
                '{
                    "resource": "subscription",
                    "id": "sub_DRjwaT5qHx",
                    "customerId": "cst_VhjQebNW5j",
                    "mode": "live",
                    "createdAt": "2018-07-17T07:45:52+00:00",
                    "status": "active",
                    "amount": {
                        "value": "' . $expectedAmountValue . '",
                        "currency": "' . $expectedAmountCurrency . '"
                    },
                    "description": "Mollie Recurring subscription #1",
                    "method": null,
                    "times": 42,
                    "interval": "15 days",
                    "startDate": "' . $expectedStartDate . '",
                    "webhookUrl": "https://example.org/webhook",
                    "_links": {
                        "self": {
                            "href": "http://api.mollie.test/v2/customers/cst_VhjQebNW5j/subscriptions/sub_DRjwaT5qHx",
                            "type": "application/hal+json"
                        },
                        "customer": {
                            "href": "http://api.mollie.test/v2/customers/cst_VhjQebNW5j",
                            "type": "application/hal+json"
                        },
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/subscriptions-api/update-subscription",
                            "type": "text/html"
                        }
                    }
                }'
            )
        );

        $subscription = $this->getSubscription();
        $expectedAmountObject = (object)[
            'value' => $expectedAmountValue,
            'currency' => $expectedAmountCurrency,
        ];
        $subscription->amount = $expectedAmountObject;
        $subscription->startDate = $expectedStartDate;

        $updatedSubscription = $subscription->update();

        $this->assertEquals($expectedStartDate, $updatedSubscription->startDate);
        $this->assertEquals($expectedAmountObject, $updatedSubscription->amount);
    }

    public function testListPageOfRootSubscriptionsWorks()
    {
        $this->mockApiCall(
            new Request('GET', '/v2/subscriptions'),
            new Response(
                200,
                [],
                '{
                    "_embedded": {
                        "subscriptions": [{
                            "resource": "subscription",
                            "id": "sub_wByQa6efm6",
                            "mode": "test",
                            "createdAt": "2018-04-24T11:41:55+00:00",
                            "status": "active",
                            "amount": {
                                "value": "10.00",
                                "currency": "EUR"
                            },
                            "description": "Order 1234",
                            "method": null,
                            "times": null,
                            "interval": "1 month",
                            "startDate": "2018-04-24",
                            "webhookUrl": null,
                            "_links": {
                                "self": {
                                    "href": "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n/subscriptions/sub_wByQa6efm6",
                                    "type": "application/hal+json"
                                },
                                "customer": {
                                    "href": "https://api.mollie.com/v2/customers/cst_FhQJRw4s2n",
                                    "type": "application/hal+json"
                                }
                            }
                        }]
                    },
                    "count": 1,
                    "_links": {
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/subscriptions-api/list-subscriptions",
                            "type": "text/html"
                        },
                        "self": {
                            "href": "https://api.mollie.com/v2/subscriptions?limit=50",
                            "type": "application/hal+json"
                        },
                        "previous": null,
                        "next": null
                    }
                }'
            )
        );

        $subscriptions = $this->apiClient->subscriptions->page();

        $this->assertInstanceOf(SubscriptionCollection::class, $subscriptions);
        $this->assertCount(1, $subscriptions);
        $subscription = $subscriptions[0];

        $this->assertEquals('subscription', $subscription->resource);
        $this->assertEquals('sub_wByQa6efm6', $subscription->id);
        // No need to test all attributes here ...
    }

    /**
     * @return Subscription
     */
    private function getSubscription()
    {
        $subscriptionJson = '{
            "resource": "subscription",
            "id": "sub_DRjwaT5qHx",
            "customerId": "cst_VhjQebNW5j",
            "mode": "live",
            "createdAt": "2018-07-17T07:45:52+00:00",
            "status": "active",
            "amount": {
                "value": "10.00",
                "currency": "EUR"
            },
            "description": "Mollie Recurring subscription #1",
            "method": null,
            "times": 42,
            "interval": "15 days",
            "startDate": "2018-12-12",
            "webhookUrl": "https://example.org/webhook",
            "_links": {
                "self": {
                    "href": "http://api.mollie.test/v2/customers/cst_VhjQebNW5j/subscriptions/sub_DRjwaT5qHx",
                    "type": "application/hal+json"
                },
                "customer": {
                    "href": "http://api.mollie.test/v2/customers/cst_VhjQebNW5j",
                    "type": "application/hal+json"
                },
                "documentation": {
                    "href": "https://docs.mollie.com/reference/v2/subscriptions-api/update-subscription",
                    "type": "text/html"
                }
            }
        }';

        return $this->copy(json_decode($subscriptionJson), new Subscription($this->apiClient));
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
