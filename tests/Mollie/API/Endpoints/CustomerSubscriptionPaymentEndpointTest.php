<?php

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Resources\Subscription;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class CustomerSubscriptionPaymentEndpointTest extends BaseEndpointTest
{
    use LinkObjectTestHelpers;

    public function testListWorks()
    {
        $this->mockApiCall(
            new Request('GET', '/v2/customers/cst_8wmqcHMN4U/subscriptions/sub_8JfGzs6v3K/payments?testmode=true'),
            new Response(
                200,
                [],
                '{
                     "_embedded": {
                         "payments": [
                             {
                                 "resource": "payment",
                                 "id": "tr_DtKxVP2AgW",
                                 "mode": "test",
                                 "createdAt": "2018-09-19T12:49:52+00:00",
                                 "amount": {
                                     "value": "10.00",
                                     "currency": "EUR"
                                 },
                                 "description": "Some subscription 19 sep. 2018",
                                 "method": "directdebit",
                                 "metadata": null,
                                 "status": "pending",
                                 "isCancelable": true,
                                 "expiresAt": "2019-09-19T12:49:52+00:00",
                                 "locale": "nl_NL",
                                 "profileId": "pfl_rH9rQtedgS",
                                 "customerId": "cst_8wmqcHMN4U",
                                 "mandateId": "mdt_aGQNkteF6w",
                                 "subscriptionId": "sub_8JfGzs6v3K",
                                 "sequenceType": "recurring",
                                 "redirectUrl": null,
                                 "webhookUrl": "https://example.org/webhook",
                                 "settlementAmount": {
                                     "value": "10.00",
                                     "currency": "EUR"
                                 },
                                 "details": {
                                     "transferReference": "SD67-6850-2204-6029",
                                     "creditorIdentifier": "NL08ZZZ502057730000",
                                     "consumerName": "Customer A",
                                     "consumerAccount": "NL50INGB0006588912",
                                     "consumerBic": "INGBNL2A",
                                     "dueDate": "2018-09-21",
                                     "signatureDate": "2018-09-19"
                                 },
                                 "_links": {
                                     "self": {
                                         "href": "https://api.mollie.com/v2/payments/tr_DtKxVP2AgW?testmode=true",
                                         "type": "application/hal+json"
                                     },
                                     "checkout": null,
                                     "customer": {
                                         "href": "https://api.mollie.com/v2/customers/cst_8wmqcHMN4U?testmode=true",
                                         "type": "application/hal+json"
                                     },
                                     "mandate": {
                                         "href": "https://api.mollie.com/v2/customers/cst_8wmqcHMN4U/mandates/mdt_aGQNkteF6w?testmode=true",
                                         "type": "application/hal+json"
                                     },
                                     "subscription": {
                                         "href": "https://api.mollie.com/v2/customers/cst_8wmqcHMN4U/subscriptions/sub_8JfGzs6v3K?testmode=true",
                                         "type": "application/hal+json"
                                     }
                                 }
                             }
                         ]
                     },
                     "count": 1,
                     "_links": {
                         "documentation": {
                             "href": "https://docs.mollie.com/reference/v2/subscriptions-api/list-subscriptions-payments",
                             "type": "text/html"
                         },
                         "self": {
                             "href": "https://api.mollie.com/v2/customers/cst_8wmqcHMN4U/subscriptions/sub_8JfGzs6v3K/payments?limit=50&testmode=true",
                             "type": "application/hal+json"
                         },
                         "previous": null,
                         "next": null
                     }
                 }'
            )
        );

        $subscription = new Subscription($this->apiClient);
        $subscription->_links = $this->createNamedLinkObject(
            'payments',
            '/v2/customers/cst_8wmqcHMN4U/subscriptions/sub_8JfGzs6v3K/payments?testmode=true',
            'application/hal+json'
        );

        $result = $subscription->payments();
        $this->assertInstanceOf(PaymentCollection::class, $result);
        $this->assertEquals(1, $result->count());
        $this->assertEquals('Some subscription 19 sep. 2018', $result[0]->description);
    }
}
