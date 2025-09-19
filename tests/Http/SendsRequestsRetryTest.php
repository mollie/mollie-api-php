<?php

namespace Tests\Http;

use Mollie\Api\Contracts\HttpAdapterContract;
use Mollie\Api\Exceptions\NetworkRequestException;
use Mollie\Api\Exceptions\RetryableNetworkRequestException;
use Mollie\Api\Http\LinearRetryStrategy;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Traits\HasDefaultFactories;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\Requests\DynamicGetRequest;

class SendsRequestsRetryTest extends TestCase
{
    /** @test */
    public function retries_retryable_network_errors_and_succeeds(): void
    {
        $attemptsToFail = 2; // will succeed on attempt 3
        $adapter = new class($attemptsToFail) implements HttpAdapterContract {
            use HasDefaultFactories;

            public int $attempts = 0;

            private int $failuresBeforeSuccess;

            public function __construct(int $failuresBeforeSuccess)
            {
                $this->failuresBeforeSuccess = $failuresBeforeSuccess;
            }

            public function sendRequest(PendingRequest $pendingRequest): Response
            {
                $this->attempts++;

                if ($this->attempts <= $this->failuresBeforeSuccess) {
                    throw new RetryableNetworkRequestException($pendingRequest, 'temporary');
                }

                $factories = $this->factories();
                // return an empty body so hydration middleware does not run
                $psrResponse = $factories->responseFactory->createResponse(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->withBody($factories->streamFactory->createStream(''));

                return new Response($psrResponse, $pendingRequest->createPsrRequest(), $pendingRequest);
            }

            public function version(): string
            {
                return 'test/adapter';
            }
        };

        $client = new MollieApiClient($adapter);
        $client->setRetryStrategy(new LinearRetryStrategy($attemptsToFail, 0)); // allow exactly enough retries, fast

        $client->setAccessToken('access_test_token');
        $response = $client->send(new DynamicGetRequest('/'));

        $this->assertSame(200, $response->status());
        $this->assertSame($attemptsToFail + 1, $adapter->attempts);
    }

    /** @test */
    public function throws_after_exhausting_retries(): void
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
        $client->setRetryStrategy(new LinearRetryStrategy(2, 0));

        $client->setAccessToken('access_test_token');
        $this->expectException(RetryableNetworkRequestException::class);

        try {
            $client->send(new DynamicGetRequest('/'));
        } finally {
            // attempts = initial try + 2 retries
            $this->assertSame(3, $adapter->attempts);
        }
    }

    /** @test */
    public function does_not_retry_on_non_retryable_exception(): void
    {
        $adapter = new class implements HttpAdapterContract {
            use HasDefaultFactories;

            public int $attempts = 0;

            public function sendRequest(PendingRequest $pendingRequest): Response
            {
                $this->attempts++;

                throw new NetworkRequestException($pendingRequest, null, 'non-retryable');
            }

            public function version(): string
            {
                return 'test/adapter';
            }
        };

        $client = new MollieApiClient($adapter);

        $client->setAccessToken('access_test_token');
        $this->expectException(NetworkRequestException::class);

        try {
            $client->send(new DynamicGetRequest('/'));
        } finally {
            // Only a single attempt should have been made
            $this->assertSame(1, $adapter->attempts);
        }
    }
}
