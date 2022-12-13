<?php

declare(strict_types=1);

namespace Tests\Mollie\Api\Endpoints;

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
                $this->getBalanceReportStub()
            )
        );

        $balance = new Balance($this->apiClient);
        $balance->id = "bal_gVMhHKqSSRYJyPsuoPNFH";

        $report = $this->apiClient->balanceReports->getFor($balance, [
            "from" => "2021-01-01",
            "until" => "2021-02-01",
            "grouping" => "transaction-categories",
        ]);

        $this->assertBalanceReport($report);
    }

    public function testCanGetPrimaryBalanceReportThroughBalanceReportEndpoint()
    {
        $this->mockApiCall(
            new Request(
                "GET",
                "/v2/balances/primary/report?from=2021-01-01&until=2021-02-01&grouping=transaction-categories"
            ),
            new Response(
                200,
                [],
                $this->getBalanceReportStub()
            )
        );

        $report = $this->apiClient->balanceReports->getForPrimary([
            "from" => "2021-01-01",
            "until" => "2021-02-01",
            "grouping" => "transaction-categories",
        ]);

        $this->assertBalanceReport($report);
    }

    private function getBalanceReportStub()
    {
        return
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
                        "href": "https://api.mollie.com/v2/balances/bal_gVMhHKqSSRYJyPsuoPNFH/report?from=2021-01-01&until=2021-02-01&grouping=transaction-categories",
                        "type": "application/hal+json"
                    }
                }
            }';
    }

    private function assertBalanceReport($report)
    {
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
        $this->assertEquals(new \stdClass, $report->totals->refunds);
        $this->assertEquals(new \stdClass, $report->totals->chargebacks);
        $this->assertEquals(new \stdClass, $report->totals->capital);
        $this->assertEquals(new \stdClass, $report->totals->transfers);

        $this->assertAmountObject(
            "0.00",
            "EUR",
            $report->totals->{"fee-prepayments"}->immediatelyAvailable->amount
        );

        $movedToAvailable = $report->totals->{"fee-prepayments"}->movedToAvailable;

        $this->assertAmountObject(
            "-0.36",
            "EUR",
            $movedToAvailable->amount
        );

        $this->assertAmountObject(
            "-0.29",
            "EUR",
            $movedToAvailable->subtotals[0]->amount
        );

        $this->assertEquals(1, $movedToAvailable->subtotals[0]->count);
        $this->assertEquals("fee", $movedToAvailable->subtotals[0]->prepaymentPartType);

        $this->assertAmountObject(
            "-0.29",
            "EUR",
            $movedToAvailable->subtotals[0]->subtotals[0]->amount
        );
        $this->assertEquals(1, $movedToAvailable->subtotals[0]->subtotals[0]->count);
        $this->assertEquals("payment-fee", $movedToAvailable->subtotals[0]->subtotals[0]->feeType);

        $this->assertAmountObject(
            "-0.29",
            "EUR",
            $movedToAvailable->subtotals[0]->subtotals[0]->subtotals[0]->amount
        );
        $this->assertEquals(1, $movedToAvailable->subtotals[0]->subtotals[0]->subtotals[0]->count);
        $this->assertEquals("ideal", $movedToAvailable->subtotals[0]->subtotals[0]->subtotals[0]->method);

        $this->assertAmountObject(
            "-0.0609",
            "EUR",
            $movedToAvailable->subtotals[1]->amount
        );
        $this->assertEquals("fee-vat", $movedToAvailable->subtotals[1]->prepaymentPartType);

        $this->assertAmountObject(
            "-0.0091",
            "EUR",
            $movedToAvailable->subtotals[2]->amount
        );
        $this->assertEquals("fee-rounding-compensation", $movedToAvailable->subtotals[2]->prepaymentPartType);

        // etc.

        $this->assertLinkObject(
            "https://docs.mollie.com/reference/v2/balances-api/get-balance-report",
            "text/html",
            $report->_links->documentation
        );

        $this->assertLinkObject(
            "https://api.mollie.com/v2/balances/bal_gVMhHKqSSRYJyPsuoPNFH/report?from=2021-01-01&until=2021-02-01&grouping=transaction-categories",
            "application/hal+json",
            $report->_links->self
        );
    }
}
