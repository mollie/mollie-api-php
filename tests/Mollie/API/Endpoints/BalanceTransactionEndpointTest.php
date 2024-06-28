<?php

declare(strict_types=1);

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Balance;
use Mollie\Api\Resources\BalanceTransaction;
use Mollie\Api\Resources\BalanceTransactionCollection;
use Mollie\Api\Resources\BaseCollection;
use Tests\Mollie\TestHelpers\AmountObjectTestHelpers;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class BalanceTransactionEndpointTest extends BaseEndpointTest
{
    use LinkObjectTestHelpers;
    use AmountObjectTestHelpers;

    public function testGetBalanceTransactionsThroughEndpoint()
    {
        $this->mockApiCall(
            new Request("GET", "/v2/balances/bal_gVMhHKqSSRYJyPsuoPNFH/transactions"),
            new Response(
                200,
                [],
                '{
                  "count": 2,
                  "_embedded": {
                    "balance_transactions": [
                       {
                         "resource": "balance_transaction",
                         "id": "baltr_QM24QwzUWR4ev4Xfgyt29A",
                         "type": "refund",
                         "resultAmount": {
                           "value": "-10.25",
                           "currency": "EUR"
                         },
                         "initialAmount": {
                           "value": "-10.00",
                           "currency": "EUR"
                         },
                         "deductions": {
                           "value": "-0.25",
                           "currency": "EUR"
                         },
                         "createdAt": "2021-01-10T12:06:28+00:00",
                         "context": {
                           "paymentId": "tr_7UhSN1zuXS",
                           "refundId": "re_4qqhO89gsT"
                         }
                       },
                       {
                         "resource": "balance_transaction",
                         "id": "baltr_QM24QwzUWR4ev4Xfgyt29B",
                         "type": "payment",
                         "resultAmount": {
                           "value": "9.71",
                           "currency": "EUR"
                         },
                         "initialAmount": {
                           "value": "10.00",
                           "currency": "EUR"
                         },
                         "deductions": {
                           "value": "-0.29",
                           "currency": "EUR"
                         },
                         "createdAt": "2021-01-10T12:06:28+00:00",
                         "context": {
                           "paymentId": "tr_7UhSN1zuXS"
                         }
                       }
                    ]
                  },
                  "_links": {
                    "documentation": {
                      "href": "https://docs.mollie.com/reference/v2/balances-api/list-balance-transactions",
                      "type": "text/html"
                    },
                    "self": {
                      "href": "https://api.mollie.com/v2/balances/bal_gVMhHKqSSRYJyPsuoPNFH/transactions?limit=5",
                      "type": "application/hal+json"
                    },
                    "previous": null,
                    "next": null
                  }
                }'
            )
        );

        $balance = new Balance($this->apiClient);
        $balance->id = "bal_gVMhHKqSSRYJyPsuoPNFH";

        $transactions = $this->apiClient->balanceTransactions->listFor($balance);

        $this->assertTransactions($transactions);
    }

    public function testIteratorForBalanceTransactionsThroughEndpoint()
    {
        $this->mockApiCall(
            new Request("GET", "/v2/balances/bal_gVMhHKqSSRYJyPsuoPNFH/transactions"),
            new Response(
                200,
                [],
                '{
                  "count": 2,
                  "_embedded": {
                    "balance_transactions": [
                       {
                         "resource": "balance_transaction",
                         "id": "baltr_QM24QwzUWR4ev4Xfgyt29A",
                         "type": "refund",
                         "resultAmount": {
                           "value": "-10.25",
                           "currency": "EUR"
                         },
                         "initialAmount": {
                           "value": "-10.00",
                           "currency": "EUR"
                         },
                         "deductions": {
                           "value": "-0.25",
                           "currency": "EUR"
                         },
                         "createdAt": "2021-01-10T12:06:28+00:00",
                         "context": {
                           "paymentId": "tr_7UhSN1zuXS",
                           "refundId": "re_4qqhO89gsT"
                         }
                       },
                       {
                         "resource": "balance_transaction",
                         "id": "baltr_QM24QwzUWR4ev4Xfgyt29B",
                         "type": "payment",
                         "resultAmount": {
                           "value": "9.71",
                           "currency": "EUR"
                         },
                         "initialAmount": {
                           "value": "10.00",
                           "currency": "EUR"
                         },
                         "deductions": {
                           "value": "-0.29",
                           "currency": "EUR"
                         },
                         "createdAt": "2021-01-10T12:06:28+00:00",
                         "context": {
                           "paymentId": "tr_7UhSN1zuXS"
                         }
                       }
                    ]
                  },
                  "_links": {
                    "documentation": {
                      "href": "https://docs.mollie.com/reference/v2/balances-api/list-balance-transactions",
                      "type": "text/html"
                    },
                    "self": {
                      "href": "https://api.mollie.com/v2/balances/bal_gVMhHKqSSRYJyPsuoPNFH/transactions?limit=5",
                      "type": "application/hal+json"
                    },
                    "previous": null,
                    "next": null
                  }
                }'
            )
        );

        $balance = new Balance($this->apiClient);
        $balance->id = "bal_gVMhHKqSSRYJyPsuoPNFH";

        foreach ($this->apiClient->balanceTransactions->iteratorFor($balance) as $balanceTransactions) {
            $this->assertInstanceOf(BalanceTransaction::class, $balanceTransactions);
        }
    }

    public function testGetPrimaryBalanceTransactionsThroughBalanceTransactionEndpoint()
    {
        $this->mockApiCall(
            new Request("GET", "/v2/balances/primary/transactions"),
            new Response(
                200,
                [],
                '{
                  "count": 2,
                  "_embedded": {
                    "balance_transactions": [
                       {
                         "resource": "balance_transaction",
                         "id": "baltr_QM24QwzUWR4ev4Xfgyt29A",
                         "type": "refund",
                         "resultAmount": {
                           "value": "-10.25",
                           "currency": "EUR"
                         },
                         "initialAmount": {
                           "value": "-10.00",
                           "currency": "EUR"
                         },
                         "deductions": {
                           "value": "-0.25",
                           "currency": "EUR"
                         },
                         "createdAt": "2021-01-10T12:06:28+00:00",
                         "context": {
                           "paymentId": "tr_7UhSN1zuXS",
                           "refundId": "re_4qqhO89gsT"
                         }
                       },
                       {
                         "resource": "balance_transaction",
                         "id": "baltr_QM24QwzUWR4ev4Xfgyt29B",
                         "type": "payment",
                         "resultAmount": {
                           "value": "9.71",
                           "currency": "EUR"
                         },
                         "initialAmount": {
                           "value": "10.00",
                           "currency": "EUR"
                         },
                         "deductions": {
                           "value": "-0.29",
                           "currency": "EUR"
                         },
                         "createdAt": "2021-01-10T12:06:28+00:00",
                         "context": {
                           "paymentId": "tr_7UhSN1zuXS"
                         }
                       }
                    ]
                  },
                  "_links": {
                    "documentation": {
                      "href": "https://docs.mollie.com/reference/v2/balances-api/list-balance-transactions",
                      "type": "text/html"
                    },
                    "self": {
                      "href": "https://api.mollie.com/v2/balances/bal_gVMhHKqSSRYJyPsuoPNFH/transactions?limit=5",
                      "type": "application/hal+json"
                    },
                    "previous": null,
                    "next": null
                  }
                }'
            )
        );

        $transactions = $this->apiClient->balanceTransactions->listForPrimary();

        $this->assertTransactions($transactions);
    }

    public function testIteratorForPrimaryBalanceTransactionsThroughBalanceTransactionEndpoint()
    {
        $this->mockApiCall(
            new Request("GET", "/v2/balances/primary/transactions"),
            new Response(
                200,
                [],
                '{
                  "count": 2,
                  "_embedded": {
                    "balance_transactions": [
                       {
                         "resource": "balance_transaction",
                         "id": "baltr_QM24QwzUWR4ev4Xfgyt29A",
                         "type": "refund",
                         "resultAmount": {
                           "value": "-10.25",
                           "currency": "EUR"
                         },
                         "initialAmount": {
                           "value": "-10.00",
                           "currency": "EUR"
                         },
                         "deductions": {
                           "value": "-0.25",
                           "currency": "EUR"
                         },
                         "createdAt": "2021-01-10T12:06:28+00:00",
                         "context": {
                           "paymentId": "tr_7UhSN1zuXS",
                           "refundId": "re_4qqhO89gsT"
                         }
                       },
                       {
                         "resource": "balance_transaction",
                         "id": "baltr_QM24QwzUWR4ev4Xfgyt29B",
                         "type": "payment",
                         "resultAmount": {
                           "value": "9.71",
                           "currency": "EUR"
                         },
                         "initialAmount": {
                           "value": "10.00",
                           "currency": "EUR"
                         },
                         "deductions": {
                           "value": "-0.29",
                           "currency": "EUR"
                         },
                         "createdAt": "2021-01-10T12:06:28+00:00",
                         "context": {
                           "paymentId": "tr_7UhSN1zuXS"
                         }
                       }
                    ]
                  },
                  "_links": {
                    "documentation": {
                      "href": "https://docs.mollie.com/reference/v2/balances-api/list-balance-transactions",
                      "type": "text/html"
                    },
                    "self": {
                      "href": "https://api.mollie.com/v2/balances/bal_gVMhHKqSSRYJyPsuoPNFH/transactions?limit=5",
                      "type": "application/hal+json"
                    },
                    "previous": null,
                    "next": null
                  }
                }'
            )
        );

        foreach ($this->apiClient->balanceTransactions->iteratorForPrimary() as $balanceTransactions) {
            $this->assertInstanceOf(BalanceTransaction::class, $balanceTransactions);
        }
    }

    private function assertTransactions(BaseCollection $transactions)
    {
        $this->assertInstanceOf(BalanceTransactionCollection::class, $transactions);
        $this->assertCount(2, $transactions);
        $this->assertEquals(2, $transactions->count());
        $this->assertLinkObject(
            "https://api.mollie.com/v2/balances/bal_gVMhHKqSSRYJyPsuoPNFH/transactions?limit=5",
            "application/hal+json",
            $transactions->_links->self
        );
        $this->assertLinkObject(
            "https://docs.mollie.com/reference/v2/balances-api/list-balance-transactions",
            "text/html",
            $transactions->_links->documentation
        );
        $this->assertNull($transactions->_links->next);
        $this->assertNull($transactions->_links->previous);

        /** @var \Mollie\Api\Resources\BalanceTransaction $transactionA */
        $transactionA = $transactions[0];

        /** @var \Mollie\Api\Resources\BalanceTransaction $transactionB */
        $transactionB = $transactions[1];

        // Transaction A
        $this->assertEquals("balance_transaction", $transactionA->resource);
        $this->assertEquals("baltr_QM24QwzUWR4ev4Xfgyt29A", $transactionA->id);
        $this->assertEquals("refund", $transactionA->type);
        $this->assertAmountObject("-10.25", "EUR", $transactionA->resultAmount);
        $this->assertAmountObject("-10.00", "EUR", $transactionA->initialAmount);
        $this->assertAmountObject("-0.25", "EUR", $transactionA->deductions);
        $this->assertEquals("2021-01-10T12:06:28+00:00", $transactionA->createdAt);
        $this->assertEquals("tr_7UhSN1zuXS", $transactionA->context->paymentId);
        $this->assertEquals("re_4qqhO89gsT", $transactionA->context->refundId);

        // Transaction B
        $this->assertEquals("balance_transaction", $transactionB->resource);
        $this->assertEquals("baltr_QM24QwzUWR4ev4Xfgyt29B", $transactionB->id);
        $this->assertEquals("payment", $transactionB->type);
        $this->assertAmountObject("9.71", "EUR", $transactionB->resultAmount);
        $this->assertAmountObject("10.00", "EUR", $transactionB->initialAmount);
        $this->assertAmountObject("-0.29", "EUR", $transactionB->deductions);
        $this->assertEquals("2021-01-10T12:06:28+00:00", $transactionB->createdAt);
        $this->assertEquals("tr_7UhSN1zuXS", $transactionB->context->paymentId);
    }
}
