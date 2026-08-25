<?php

declare(strict_types=1);

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedBalanceTransactionRequest;
use Mollie\Api\Resources\Balance;
use Mollie\Api\Resources\BalanceTransaction;
use Mollie\Api\Resources\BalanceTransactionCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BalanceTransactionEndpointCollectionTest extends TestCase
{
    #[Test]
    public function primary_iterator_forwards_testmode()
    {
        $client = new MockMollieClient([
            GetPaginatedBalanceTransactionRequest::class => MockResponse::ok('empty-list', 'balance_transactions'),
        ]);

        $client->balanceTransactions->iteratorForPrimary([], false, true);

        $client->assertSent(function (PendingRequest $pendingRequest) {
            $this->assertSame('balances/primary/transactions', $pendingRequest->getRequest()->resolveResourcePath());
            $this->assertSame(['testmode' => 'true'], $pendingRequest->query()->all());

            return true;
        });
    }

    #[Test]
    public function page_paths_forward_testmode()
    {
        $calls = [
            fn (MockMollieClient $client) => $client->balanceTransactions->pageFor($this->balance($client), [], true),
            fn (MockMollieClient $client) => $client->balanceTransactions->pageForId('bal_specific', [], true),
            fn (MockMollieClient $client) => $client->balanceTransactions->pageForPrimary([], true),
        ];

        foreach ($calls as $call) {
            $this->assertInitialTestmodeRequest($call);
        }
    }

    #[Test]
    public function iterator_paths_forward_testmode()
    {
        $calls = [
            fn (MockMollieClient $client) => $client->balanceTransactions->iteratorFor($this->balance($client), [], false, true),
            fn (MockMollieClient $client) => $client->balanceTransactions->iteratorForId('bal_specific', [], false, true),
            fn (MockMollieClient $client) => $client->balanceTransactions->iteratorForPrimary([], false, true),
        ];

        foreach ($calls as $call) {
            $this->assertInitialTestmodeRequest($call);
        }
    }

    #[Test]
    public function explicit_false_and_primary_null_omit_testmode()
    {
        $calls = [
            fn (MockMollieClient $client) => $client->balanceTransactions->pageForPrimary([], false),
            fn (MockMollieClient $client) => $client->balanceTransactions->iteratorForPrimary([], false, null),
        ];

        foreach ($calls as $call) {
            $client = $this->clientForEmptyTransactions();
            $call($client);

            $client->assertSent(function (PendingRequest $pendingRequest) {
                $this->assertFalse($pendingRequest->query()->has('testmode'));
                $this->assertFalse($pendingRequest->getTestmode());

                return true;
            });
        }
    }

    #[Test]
    public function query_testmode_takes_precedence_and_is_removed_from_pagination_input()
    {
        $client = $this->clientForEmptyTransactions();
        $client->balanceTransactions->pageForId('bal_specific', [
            'from' => 'baltr_page',
            'limit' => 25,
            'testmode' => true,
        ], false);

        $client->assertSent(function (PendingRequest $pendingRequest) {
            $this->assertSame([
                'from' => 'baltr_page',
                'limit' => 25,
                'testmode' => 'true',
            ], $pendingRequest->query()->all());
            $this->assertTrue($pendingRequest->getRequest()->getTestmode());

            return true;
        });

        $client = $this->clientForEmptyTransactions();
        $client->balanceTransactions->iteratorForId('bal_specific', [
            'from' => 'baltr_page',
            'limit' => 25,
            'testmode' => false,
        ], false, true);

        $client->assertSent(function (PendingRequest $pendingRequest) {
            $this->assertSame([
                'from' => 'baltr_page',
                'limit' => 25,
            ], $pendingRequest->query()->all());
            $this->assertFalse($pendingRequest->getRequest()->getTestmode());

            return true;
        });
    }

    #[Test]
    public function api_key_authentication_strips_explicit_testmode()
    {
        $client = $this->clientForEmptyTransactions();
        $client->setApiKey('test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM');

        $client->balanceTransactions->pageForPrimary([], true);

        $client->assertSent(function (PendingRequest $pendingRequest) {
            $this->assertFalse($pendingRequest->query()->has('testmode'));
            $this->assertTrue($pendingRequest->getTestmode());

            return true;
        });
    }

    #[Test]
    public function forward_iterator_retains_testmode_on_follow_up_requests()
    {
        $nextUrl = 'https://api.mollie.com/v2/balances/bal_specific/transactions?from=baltr_page2&testmode=true';
        $client = new MockMollieClient([
            GetPaginatedBalanceTransactionRequest::class => MockResponse::ok($this->cursorPage('baltr_page1', null, $nextUrl)),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'balance_transactions'),
        ], true);

        iterator_to_array($client->balanceTransactions->iteratorForId('bal_specific', [], false, true));

        $client->assertSent(function (PendingRequest $pendingRequest): bool {
            if (! $pendingRequest->getRequest() instanceof GetPaginatedBalanceTransactionRequest) {
                return false;
            }

            $this->assertTrue($pendingRequest->getTestmode());
            $this->assertSame('true', $pendingRequest->query()->get('testmode'));

            return true;
        });

        $client->assertSent(function (PendingRequest $pendingRequest): bool {
            if (! $pendingRequest->getRequest() instanceof DynamicGetRequest) {
                return false;
            }

            $uri = (string) $pendingRequest->createPsrRequest()->getUri();
            parse_str((string) parse_url($uri, PHP_URL_QUERY), $query);

            $this->assertTrue($pendingRequest->getTestmode());
            $this->assertSame('/v2/balances/bal_specific/transactions', parse_url($uri, PHP_URL_PATH));
            $this->assertSame('baltr_page2', $query['from'] ?? null);
            $this->assertSame('true', $query['testmode'] ?? null);

            return true;
        });
    }

    #[Test]
    public function reverse_iterator_retains_live_mode_on_follow_up_requests()
    {
        $previousUrl = 'https://api.mollie.com/v2/balances/bal_specific/transactions?from=baltr_page1';
        $client = new MockMollieClient([
            GetPaginatedBalanceTransactionRequest::class => MockResponse::ok($this->cursorPage('baltr_page2', $previousUrl)),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'balance_transactions'),
        ], true);

        iterator_to_array($client->balanceTransactions->iteratorForId('bal_specific', [], true));

        $client->assertSent(fn (PendingRequest $pendingRequest): bool => $pendingRequest->getRequest() instanceof DynamicGetRequest
            && ! $pendingRequest->getTestmode()
            && ! str_contains((string) $pendingRequest->createPsrRequest()->getUri(), 'testmode='));
    }

    #[Test]
    public function page_for()
    {
        $client = new MockMollieClient([
            GetPaginatedBalanceTransactionRequest::class => MockResponse::ok('balance-transaction-list'),
        ]);

        $balance = new Balance($client);
        $balance->id = 'bal_gVMhHKqSSRYJyPsuoPNFH';

        /** @var BalanceTransactionCollection $transactions */
        $transactions = $client->balanceTransactions->pageFor($balance);

        $this->assertInstanceOf(BalanceTransactionCollection::class, $transactions);
        $this->assertGreaterThan(0, $transactions->count());

        foreach ($transactions as $transaction) {
            $this->assertBalanceTransaction($transaction);
        }
    }

    #[Test]
    public function iterator_for()
    {
        $client = new MockMollieClient([
            GetPaginatedBalanceTransactionRequest::class => MockResponse::ok('balance-transaction-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'balance_transactions'),
        ]);

        $balance = new Balance($client);
        $balance->id = 'bal_gVMhHKqSSRYJyPsuoPNFH';

        foreach ($client->balanceTransactions->iteratorFor($balance) as $transaction) {
            $this->assertBalanceTransaction($transaction);
        }
    }

    protected function assertBalanceTransaction(BalanceTransaction $transaction)
    {
        $this->assertInstanceOf(BalanceTransaction::class, $transaction);
        $this->assertEquals('balance_transaction', $transaction->resource);
        $this->assertNotEmpty($transaction->id);
        $this->assertNotEmpty($transaction->type);
        $this->assertNotEmpty($transaction->resultAmount);
        $this->assertNotEmpty($transaction->initialAmount);
        $this->assertNotEmpty($transaction->deductions);
        $this->assertNotEmpty($transaction->context);
    }

    private function clientForEmptyTransactions(): MockMollieClient
    {
        return new MockMollieClient([
            GetPaginatedBalanceTransactionRequest::class => MockResponse::ok('empty-list', 'balance_transactions'),
        ]);
    }

    private function balance(MockMollieClient $client): Balance
    {
        $balance = new Balance($client);
        $balance->id = 'bal_specific';

        return $balance;
    }

    private function assertInitialTestmodeRequest(callable $call): void
    {
        $client = $this->clientForEmptyTransactions();
        $call($client);

        $client->assertSent(function (PendingRequest $pendingRequest) {
            $this->assertSame('true', $pendingRequest->query()->get('testmode'));
            $this->assertTrue($pendingRequest->getRequest()->getTestmode());

            return true;
        });
    }

    private function cursorPage(string $id, ?string $previous = null, ?string $next = null): array
    {
        $links = [];

        if ($previous !== null) {
            $links['previous'] = ['href' => $previous];
        }

        if ($next !== null) {
            $links['next'] = ['href' => $next];
        }

        return [
            '_links' => $links,
            '_embedded' => [
                'balance_transactions' => [
                    ['id' => $id],
                ],
            ],
        ];
    }
}
