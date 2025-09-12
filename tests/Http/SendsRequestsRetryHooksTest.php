<?php

namespace Tests\Http;

use Mollie\Api\Contracts\HttpAdapterContract;
use Mollie\Api\Exceptions\RetryableNetworkRequestException;
use Mollie\Api\Http\LinearRetryStrategy;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Traits\HasDefaultFactories;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\Requests\DynamicGetRequest;

class SendsRequestsRetryHooksTest extends TestCase
{
    /** @test */
    public function fatal_middleware_runs_once_after_retries_exhausted(): void
    {
        $adapter = new class implements HttpAdapterContract {
            use HasDefaultFactories;

            public int $attempts = 0;

            public function sendRequest(PendingRequest $pendingRequest): Response
            {
                $this->attempts++;

                throw new RetryableNetworkRequestException($pendingRequest, 'temporary');
            }

            public function version(): string
            {
                return 'test/adapter';
            }
        };

        $client = new MollieApiClient($adapter);
        $client->setAccessToken('access_test_token');
        $client->setRetryStrategy(new LinearRetryStrategy(2, 0));

        $fatalCount = 0;
        $client->middleware()->onFatal(function () use (&$fatalCount) {
            $fatalCount++;
        });

        try {
            $client->send(new DynamicGetRequest('/'));
            $this->fail('Expected exception not thrown');
        } catch (RetryableNetworkRequestException $e) {
            // expected
        }

        // attempts: 1 initial + 2 retries
        $this->assertSame(3, $adapter->attempts);
        // fatal middleware should be invoked exactly once when retries are exhausted
        $this->assertSame(1, $fatalCount);
    }
}
