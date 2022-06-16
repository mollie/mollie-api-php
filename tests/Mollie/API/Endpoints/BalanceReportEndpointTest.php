<?php

declare(strict_types=1);

namespace Tests\Mollie\API\Endpoints;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Resources\Balance;
use Mollie\Api\Resources\BalanceReport;
use Tests\Mollie\TestHelpers\AmountObjectTestHelpers;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class BalanceReportEndpointTest extends BaseEndpointTest
{
    use AmountObjectTestHelpers;
    use LinkObjectTestHelpers;

    public function testCanGetReportThroughBalanceReportEndpoint()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/balances/bal_gVMhHKqSSRYJyPsuoPNFH/report?from=2021-01-01&until=2021-02-01&grouping=transaction-categories"
            ),
            new Response(
                200,
                [],
                '{
                    "resource": "balance-report",
                    "balanceId": "bal_gVMhHKqSSRYJyPsuoPNFH",
                    "timeZone": "Europe/Amsterdam",
                    "from": "2021-01-01",
                    "until": "2021-01-31",
                    "grouping": "transaction-categories",
                    "totals": {
                        "open": {
                            "available": {
                                "amount": {
                                    "currency": "EUR",
                                    "value": "0.00"
                                }
                            },
                            "pending": {
                                "amount": {
                                    "currency": "EUR",
                                    "value": "0.00"
                                }
                            }
                        },
                        "payments": {
                            "immediatelyAvailable": {
                                "amount": {
                                    "currency": "EUR",
                                    "value": "0.00"
                                }
                            },
                            "pending": {
                                "amount": {
                                    "currency": "EUR",
                                    "value": "4.98"
                                },
                                "subtotals": [
                                    {
                                        "transactionType": "payment",
                                        "count": 1,
                                        "amount": {
                                            "currency": "EUR",
                                            "value": "4.98"
                                        },
                                        "subtotals": [
                                            {
                                                "amount": {
                                                "currency": "EUR",
                                                    "value": "4.98"
                                                },
                                                "count": 1,
                                                "method": "ideal"
                                            }
                                        ]
                                    }
                                ]
                            },
                            "movedToAvailable": {
                                "amount": {
                                    "currency": "EUR",
                                    "value": "0.00"
                                }
                            }
                        },
                        "refunds": {},
                        "chargebacks": {},
                        "capital": {},
                        "transfers": {},
                        "fee-prepayments": {
                            "immediatelyAvailable": {
                                "amount": {
                                    "currency": "EUR",
                                    "value": "0.00"
                                }
                            },
                            "movedToAvailable": {
                                "amount": {
                                    "currency": "EUR",
                                    "value": "-0.36"
                                },
                                "subtotals": [
                                    {
                                        "amount": {
                                            "currency": "EUR",
                                            "value": "-0.29"
                                        },
                                        "count": 1,
                                        "prepaymentPartType": "fee",
                                        "subtotals": [
                                            {
                                                "amount": {
                                                    "currency": "EUR",
                                                    "value": "-0.29"
                                                },
                                                "count": 1,
                                                "feeType": "payment-fee",
                                                "subtotals": [
                                                    {
                                                        "amount": {
                                                            "currency": "EUR",
                                                            "value": "-0.29"
                                                        },
                                                        "count": 1,
                                                        "method": "ideal"
                                                    }
                                                ]
                                            }
                                        ]
                                    },
                                    {
                                        "amount": {
                                            "currency": "EUR",
                                            "value": "-0.0609"
                                        },
                                        "prepaymentPartType": "fee-vat"
                                    },
                                    {
                                        "amount": {
                                            "currency": "EUR",
                                            "value": "-0.0091"
                                        },
                                        "prepaymentPartType": "fee-rounding-compensation"
                                    }
                                ]
                            },
                            "pending": {
                                "amount": {
                                    "currency": "EUR",
                                    "value": "-0.36"
                                },
                                "subtotals": [
                                    {
                                        "amount": {
                                            "currency": "EUR",
                                            "value": "-0.29"
                                        },
                                        "count": 1,
                                        "prepaymentPartType": "fee",
                                        "subtotals": [
                                            {
                                                "amount": {
                                                    "currency": "EUR",
                                                    "value": "-0.29"
                                                },
                                                "count": 1,
                                                "feeType": "payment-fee",
                                                "subtotals": [
                                                    {
                                                        "amount": {
                                                            "currency": "EUR",
                                                            "value": "-0.29"
                                                        },
                                                        "count": 1,
                                                        "method": "ideal"
                                                    }
                                                ]
                                            }
                                        ]
                                    },
                                    {
                                        "amount": {
                                            "currency": "EUR",
                                            "value": "-0.0609"
                                        },
                                        "prepaymentPartType": "fee-vat"
                                    },
                                    {
                                        "amount": {
                                            "currency": "EUR",
                                            "value": "-0.0091"
                                        },
                                        "prepaymentPartType": "fee-rounding-compensation"
                                    }
                                ]
                            }
                        },
                        "corrections": {},
                        "close": {
                            "available": {
                                "amount": {
                                    "currency": "EUR",
                                    "value": "0.00"
                                }
                            },
                            "pending": {
                                "amount": {
                                    "currency": "EUR",
                                    "value": "4.32"
                                }
                            }
                        }
                    },
                    "_links": {
                        "documentation": {
                            "href": "https://docs.mollie.com/reference/v2/balances-api/get-balance-report",
                            "type": "text/html"
                        },
                        "self": {
                            "href": "https://api.mollie.com/v2/balances/{balanceId}/report?from=2021-01-01&until=2021-02-01&grouping=transaction-categories",
                            "type": "application/hal+json"
                        }
                    }
                }'
            )
        );

        $balance = new Balance($this->apiClient);
        $balance->id = "bal_gVMhHKqSSRYJyPsuoPNFH";

        $report = $this->apiClient->balanceReports->getForBalance($balance, [
            "from" => "2021-01-01",
            "until" => "2021-02-01",
            "grouping" => "transaction-categories",
        ]);

        $this->assertInstanceOf(BalanceReport::class, $report);
        $this->assertEquals("balance-report", $report->resource);
        $this->assertEquals("bal_gVMhHKqSSRYJyPsuoPNFH", $report->balanceId);
        $this->assertEquals("Europe/Amsterdam", $report->timeZone);
        $this->assertEquals($report->from, "2021-01-01");
        $this->assertEquals($report->until, "2021-01-31");
        $this->assertEquals($report->grouping, "transaction-categories");
        $this->assertAmountObject('0.00', 'EUR', $report->totals->open->available->amount);
        $this->assertAmountObject('0.00', 'EUR', $report->totals->open->pending->amount);
        $this->assertAmountObject(
            '0.00',
            'EUR',
            $report->totals->payments->immediatelyAvailable->amount
        );
        $this->assertAmountObject(
            '4.98',
            'EUR',
            $report->totals->payments->pending->amount
        );
        $this->assertAmountObject(
            '4.98',
            'EUR',
            $report->totals->payments->pending->subtotals[0]->amount
        );
        $this->assertEquals(
            "payment",
            $report->totals->payments->pending->subtotals[0]->transactionType
        );
        $this->assertEquals(
            1,
            $report->totals->payments->pending->subtotals[0]->count
        );
        $this->assertAmountObject(
            "4.98",
            "EUR",
            $report->totals->payments->pending->subtotals[0]->subtotals[0]->amount
        );
        $this->assertEquals(
            1,
            $report->totals->payments->pending->subtotals[0]->subtotals[0]->count
        );
        $this->assertEquals(
            "ideal",
            $report->totals->payments->pending->subtotals[0]->subtotals[0]->method
        );
        $this->assertAmountObject(
            "0.00",
            "EUR",
            $report->totals->payments->movedToAvailable->amount
        );
        // TODO here

        $this->markTestIncomplete("TBI BalanceReportEndpointTest");
    }

    public function testCanGetReportThroughBalanceResource()
    {
        $this->markTestIncomplete("TBI BalanceReportEndpointTest");
    }

    public function testCanGetReportForPrimaryThroughBalanceReportEndpoint()
    {
        $this->markTestIncomplete("TBI BalanceReportEndpointTest");
    }
}
