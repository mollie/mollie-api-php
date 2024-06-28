<?php

declare(strict_types=1);

namespace Tests\Mollie\Api\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Balance;
use Mollie\Api\Resources\BalanceCollection;
use Mollie\Api\Types\BalanceTransferFrequency;
use Tests\Mollie\TestHelpers\AmountObjectTestHelpers;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class BalanceEndpointTest extends BaseEndpointTest
{
    use LinkObjectTestHelpers;
    use AmountObjectTestHelpers;

    public function testListBalances()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/balances"
            ),
            new Response(
                200,
                [],
                '{
                     "count": 2,
                     "_embedded": {
                       "balances": [
                          {
                            "resource": "balance",
                            "id": "bal_gVMhHKqSSRYJyPsuoPNFH",
                            "mode": "live",
                            "createdAt": "2019-01-10T12:06:28+00:00",
                            "currency": "EUR",
                            "status": "active",
                            "availableAmount": {
                              "value": "0.00",
                              "currency": "EUR"
                            },
                            "incomingAmount": {
                              "value": "0.00",
                              "currency": "EUR"
                            },
                            "outgoingAmount": {
                              "value": "0.00",
                              "currency": "EUR"
                            },
                            "transferFrequency": "daily",
                            "transferThreshold": {
                              "value": "40.00",
                              "currency": "EUR"
                            },
                            "transferReference": "Mollie payout",
                            "transferDestination": {
                              "type": "bank-account",
                              "beneficiaryName": "Jack Bauer",
                              "bankAccount": "NL53INGB0654422370",
                              "bankAccountId": "bnk_jrty3f"
                            },
                            "_links": {
                              "self": {
                                "href": "https://api.mollie.com/v2/balances/bal_gVMhHKqSSRYJyPsuoPNFH",
                                "type": "application/hal+json"
                              }
                            }
                          },
                          {
                            "resource": "balance",
                            "id": "bal_gVMhHKqSSRYJyPsuoPABC",
                            "mode": "live",
                            "createdAt": "2019-01-10T10:23:41+00:00",
                            "status": "active",
                            "currency": "EUR",
                            "availableAmount": {
                              "value": "0.00",
                              "currency": "EUR"
                            },
                            "incomingAmount": {
                              "value": "0.00",
                              "currency": "EUR"
                            },
                            "outgoingAmount": {
                              "value": "0.00",
                              "currency": "EUR"
                            },
                            "transferFrequency": "twice-a-month",
                            "transferThreshold": {
                              "value": "5.00",
                              "currency": "EUR"
                            },
                            "transferReference": "Mollie payout",
                            "transferDestination": {
                              "type": "bank-account",
                              "beneficiaryName": "Jack Bauer",
                              "bankAccount": "NL97MOLL6351480700",
                              "bankAccountId": "bnk_jrty3e"
                            },
                            "_links": {
                              "self": {
                                "href": "https://api.mollie.com/v2/balances/bal_gVMhHKqSSRYJyPsuoPABC",
                                "type": "application/hal+json"
                              }
                            }
                          }
                       ]
                     },
                     "_links": {
                       "documentation": {
                         "href": "https://docs.mollie.com/reference/v2/balances-api/list-balances",
                         "type": "text/html"
                       },
                       "self": {
                         "href": "https://api.mollie.com/v2/balances?limit=5",
                         "type": "application/hal+json"
                       },
                       "previous": null,
                       "next": {
                         "href": "https://api.mollie.com/v2/balances?from=bal_gVMhHKqSSRYJyPsuoPABC&limit=5",
                         "type": "application/hal+json"
                       }
                     }
                   }'
            )
        );

        /** @var BalanceCollection $balances */
        $balances = $this->apiClient->balances->page();

        $this->assertInstanceOf(BalanceCollection::class, $balances);
        $this->assertEquals(2, $balances->count());
        $this->assertCount(2, $balances);

        $this->assertLinkObject(
            "https://docs.mollie.com/reference/v2/balances-api/list-balances",
            "text/html",
            $balances->_links->documentation
        );

        $this->assertLinkObject(
            "https://api.mollie.com/v2/balances?limit=5",
            "application/hal+json",
            $balances->_links->self
        );

        $this->assertLinkObject(
            "https://api.mollie.com/v2/balances?from=bal_gVMhHKqSSRYJyPsuoPABC&limit=5",
            "application/hal+json",
            $balances->_links->next
        );

        /** @var Balance $balanceA */
        $balanceA = $balances[0];

        /** @var Balance $balanceB */
        $balanceB = $balances[1];

        $this->assertBalance(
            $balanceA,
            "bal_gVMhHKqSSRYJyPsuoPNFH",
            "2019-01-10T12:06:28+00:00",
            BalanceTransferFrequency::DAILY,
            "40.00",
            (object) [
                "type" => "bank-account",
                "beneficiaryName" => "Jack Bauer",
                "bankAccount" => "NL53INGB0654422370",
                "bankAccountId" => "bnk_jrty3f",
            ]
        );
        $this->assertBalance(
            $balanceB,
            "bal_gVMhHKqSSRYJyPsuoPABC",
            "2019-01-10T10:23:41+00:00",
            BalanceTransferFrequency::TWICE_A_MONTH,
            "5.00",
            (object) [
                "type" => "bank-account",
                "beneficiaryName" => "Jack Bauer",
                "bankAccount" => "NL97MOLL6351480700",
                "bankAccountId" => "bnk_jrty3e",
            ]
        );
    }

    public function testIterateBalances()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/balances"
            ),
            new Response(
                200,
                [],
                '{
                     "count": 2,
                     "_embedded": {
                       "balances": [
                          {
                            "resource": "balance",
                            "id": "bal_gVMhHKqSSRYJyPsuoPNFH",
                            "mode": "live",
                            "createdAt": "2019-01-10T12:06:28+00:00",
                            "currency": "EUR",
                            "status": "active",
                            "availableAmount": {
                              "value": "0.00",
                              "currency": "EUR"
                            },
                            "incomingAmount": {
                              "value": "0.00",
                              "currency": "EUR"
                            },
                            "outgoingAmount": {
                              "value": "0.00",
                              "currency": "EUR"
                            },
                            "transferFrequency": "daily",
                            "transferThreshold": {
                              "value": "40.00",
                              "currency": "EUR"
                            },
                            "transferReference": "Mollie payout",
                            "transferDestination": {
                              "type": "bank-account",
                              "beneficiaryName": "Jack Bauer",
                              "bankAccount": "NL53INGB0654422370",
                              "bankAccountId": "bnk_jrty3f"
                            },
                            "_links": {
                              "self": {
                                "href": "https://api.mollie.com/v2/balances/bal_gVMhHKqSSRYJyPsuoPNFH",
                                "type": "application/hal+json"
                              }
                            }
                          },
                          {
                            "resource": "balance",
                            "id": "bal_gVMhHKqSSRYJyPsuoPABC",
                            "mode": "live",
                            "createdAt": "2019-01-10T10:23:41+00:00",
                            "status": "active",
                            "currency": "EUR",
                            "availableAmount": {
                              "value": "0.00",
                              "currency": "EUR"
                            },
                            "incomingAmount": {
                              "value": "0.00",
                              "currency": "EUR"
                            },
                            "outgoingAmount": {
                              "value": "0.00",
                              "currency": "EUR"
                            },
                            "transferFrequency": "twice-a-month",
                            "transferThreshold": {
                              "value": "5.00",
                              "currency": "EUR"
                            },
                            "transferReference": "Mollie payout",
                            "transferDestination": {
                              "type": "bank-account",
                              "beneficiaryName": "Jack Bauer",
                              "bankAccount": "NL97MOLL6351480700",
                              "bankAccountId": "bnk_jrty3e"
                            },
                            "_links": {
                              "self": {
                                "href": "https://api.mollie.com/v2/balances/bal_gVMhHKqSSRYJyPsuoPABC",
                                "type": "application/hal+json"
                              }
                            }
                          }
                       ]
                     },
                     "_links": {
                       "documentation": {
                         "href": "https://docs.mollie.com/reference/v2/balances-api/list-balances",
                         "type": "text/html"
                       },
                       "self": {
                         "href": "https://api.mollie.com/v2/balances?limit=5",
                         "type": "application/hal+json"
                       },
                       "previous": null,
                       "next": null
                     }
                   }'
            )
        );

        foreach ($this->apiClient->balances->iterator() as $balance) {
            $this->assertInstanceOf(Balance::class, $balance);
        }
    }

    public function testGetBalance()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/balances/bal_gVMhHKqSSRYJyPsuoPNFH"
            ),
            new Response(
                200,
                [],
                '{
                     "resource": "balance",
                     "id": "bal_gVMhHKqSSRYJyPsuoPNFH",
                     "mode": "live",
                     "createdAt": "2019-01-10T10:23:41+00:00",
                     "currency": "EUR",
                     "status": "active",
                     "availableAmount": {
                       "value": "0.00",
                       "currency": "EUR"
                     },
                     "incomingAmount": {
                       "value": "0.00",
                       "currency": "EUR"
                     },
                     "outgoingAmount": {
                       "value": "0.00",
                       "currency": "EUR"
                     },
                     "transferFrequency": "twice-a-month",
                     "transferThreshold": {
                       "value": "5.00",
                       "currency": "EUR"
                     },
                    "transferReference": "Mollie payout",
                     "transferDestination": {
                       "type": "bank-account",
                       "beneficiaryName": "Jack Bauer",
                       "bankAccount": "NL53INGB0654422370",
                       "bankAccountId": "bnk_jrty3f"
                     },
                     "_links": {
                       "self": {
                         "href": "https://api.mollie.com/v2/balances/bal_gVMhHKqSSRYJyPsuoPNFH",
                         "type": "application/hal+json"
                       },
                       "documentation": {
                         "href": "https://docs.mollie.com/reference/v2/balances-api/get-balance",
                         "type": "text/html"
                       }
                     }
                   }'
            )
        );

        /** @var Balance $balance */
        $balance = $this->apiClient->balances->get("bal_gVMhHKqSSRYJyPsuoPNFH");

        $this->assertBalance(
            $balance,
            "bal_gVMhHKqSSRYJyPsuoPNFH",
            "2019-01-10T10:23:41+00:00",
            BalanceTransferFrequency::TWICE_A_MONTH,
            "5.00",
            (object) [
                'type' => 'bank-account',
                'beneficiaryName' => 'Jack Bauer',
                'bankAccount' => 'NL53INGB0654422370',
                'bankAccountId' => 'bnk_jrty3f',
            ]
        );
    }

    public function testGetPrimaryBalance()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/balances/primary"
            ),
            new Response(
                200,
                [],
                '{
                     "resource": "balance",
                     "id": "bal_gVMhHKqSSRYJyPsuoPNFH",
                     "mode": "live",
                     "createdAt": "2019-01-10T10:23:41+00:00",
                     "currency": "EUR",
                     "status": "active",
                     "availableAmount": {
                       "value": "0.00",
                       "currency": "EUR"
                     },
                     "incomingAmount": {
                       "value": "0.00",
                       "currency": "EUR"
                     },
                     "outgoingAmount": {
                       "value": "0.00",
                       "currency": "EUR"
                     },
                     "transferFrequency": "twice-a-month",
                     "transferThreshold": {
                       "value": "5.00",
                       "currency": "EUR"
                     },
                    "transferReference": "Mollie payout",
                     "transferDestination": {
                       "type": "bank-account",
                       "beneficiaryName": "Jack Bauer",
                       "bankAccount": "NL53INGB0654422370",
                       "bankAccountId": "bnk_jrty3f"
                     },
                     "_links": {
                       "self": {
                         "href": "https://api.mollie.com/v2/balances/bal_gVMhHKqSSRYJyPsuoPNFH",
                         "type": "application/hal+json"
                       },
                       "documentation": {
                         "href": "https://docs.mollie.com/reference/v2/balances-api/get-balance",
                         "type": "text/html"
                       }
                     }
                   }'
            )
        );

        /** @var Balance $balance */
        $balance = $this->apiClient->balances->primary();

        $this->assertBalance(
            $balance,
            "bal_gVMhHKqSSRYJyPsuoPNFH",
            "2019-01-10T10:23:41+00:00",
            BalanceTransferFrequency::TWICE_A_MONTH,
            "5.00",
            (object) [
                'type' => 'bank-account',
                'beneficiaryName' => 'Jack Bauer',
                'bankAccount' => 'NL53INGB0654422370',
                'bankAccountId' => 'bnk_jrty3f',
            ]
        );
    }

    /**
     * @param \Mollie\Api\Resources\Balance $balance
     * @param string $balanceId
     * @param string $createdAt
     * @param string $transferFrequency
     * @param string $thresholdValue
     * @param \stdClass $destination
     * @return void
     */
    protected function assertBalance(
        Balance $balance,
        string $balanceId,
        string $createdAt,
        string $transferFrequency,
        string $thresholdValue,
        \stdClass $destination
    ) {
        $this->assertInstanceOf(Balance::class, $balance);
        $this->assertEquals("balance", $balance->resource);
        $this->assertEquals($balanceId, $balance->id);

        $this->assertEquals("live", $balance->mode);
        $this->assertEquals($createdAt, $balance->createdAt);
        $this->assertEquals("EUR", $balance->currency);
        $this->assertAmountObject("0.00", "EUR", $balance->availableAmount);
        $this->assertAmountObject("0.00", "EUR", $balance->incomingAmount);
        $this->assertAmountObject("0.00", "EUR", $balance->outgoingAmount);
        $this->assertEquals($transferFrequency, $balance->transferFrequency);
        $this->assertAmountObject($thresholdValue, "EUR", $balance->transferThreshold);
        $this->assertEquals("Mollie payout", $balance->transferReference);
        $this->assertEquals($destination, $balance->transferDestination);

        $this->assertLinkObject(
            "https://api.mollie.com/v2/balances/{$balanceId}",
            "application/hal+json",
            $balance->_links->self
        );
    }
}
