<?php

namespace Tests\Http;

use Mollie\Api\Contracts\HttpAdapterContract;
use Mollie\Api\Exceptions\RetryableNetworkRequestException;
use Mollie\Api\Exceptions\TooManyRequestsException;
use Mollie\Api\Http\ExponentialRetryStrategy;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Traits\HasDefaultFactories;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\Requests\DynamicGetRequest;

class ExponentialRetryStrategyTest extends TestCase
{
    /** @test */
    public function it_retries_network_errors_and_rate_limits(): void
    {
        $strategy = new ExponentialRetryStrategy;
        /** @var PendingRequest $pendingRequest */
        $pendingRequest = (new \ReflectionClass(PendingRequest::class))->newInstanceWithoutConstructor();

        $this->assertTrue($strategy->shouldRetry(
            new RetryableNetworkRequestException($pendingRequest, 'boom')
        ));
        $this->assertTrue($strategy->shouldRetry(
            new TooManyRequestsException($this->makeResponse(), 'slow down', 429, null, 7)
        ));
        $this->assertFalse($strategy->shouldRetry(new \RuntimeException));
    }

    /** @test */
    public function it_does_not_retry_retry_after_values_over_budget(): void
    {
        $strategy = new ExponentialRetryStrategy(3, 500, 2.0, 10000);

        $this->assertFalse($strategy->shouldRetry(
            new TooManyRequestsException($this->makeResponse(), 'slow', 429, null, 11)
        ));
        $this->assertTrue($strategy->shouldRetry(
            new TooManyRequestsException($this->makeResponse(), 'slow', 429, null, 10)
        ));
    }

    /** @test */
    public function it_honors_retry_after_within_budget(): void
    {
        $strategy = new ExponentialRetryStrategy(3, 500, 2.0, 30000, false);
        $exception = new TooManyRequestsException($this->makeResponse(), 'slow', 429, null, 12);

        $this->assertSame(12000, $strategy->delayBeforeAttemptMs(1, $exception));
    }

    /** @test */
    public function it_falls_back_to_capped_exponential_delays(): void
    {
        $strategy = new ExponentialRetryStrategy(10, 100, 2.0, 500, false);

        $this->assertSame(100, $strategy->delayBeforeAttemptMs(1));
        $this->assertSame(200, $strategy->delayBeforeAttemptMs(2));
        $this->assertSame(400, $strategy->delayBeforeAttemptMs(3));
        $this->assertSame(500, $strategy->delayBeforeAttemptMs(4));
    }

    /** @test */
    public function exponential_delay_is_capped_before_integer_overflow(): void
    {
        $strategy = new ExponentialRetryStrategy(3, 500, 2.0, 30000, false);

        $this->assertSame(30000, $strategy->delayBeforeAttemptMs(PHP_INT_MAX));
    }

    /** @test */
    public function full_jitter_stays_within_the_exponential_delay(): void
    {
        $strategy = new ExponentialRetryStrategy(3, 1000, 2.0, 30000, true);

        for ($iteration = 0; $iteration < 20; $iteration++) {
            $delay = $strategy->delayBeforeAttemptMs(1);
            $this->assertGreaterThanOrEqual(0, $delay);
            $this->assertLessThanOrEqual(1000, $delay);
        }
    }

    /** @test */
    public function retry_after_jitter_is_additive_and_bounded(): void
    {
        $strategy = new ExponentialRetryStrategy(3, 500, 2.0, 10000, true);
        $exception = new TooManyRequestsException($this->makeResponse(), 'slow', 429, null, 10);

        for ($iteration = 0; $iteration < 20; $iteration++) {
            $delay = $strategy->delayBeforeAttemptMs(1, $exception);
            $this->assertGreaterThanOrEqual(10000, $delay);
            $this->assertLessThanOrEqual(11000, $delay);
        }
    }

    /** @test */
    public function retry_after_jitter_is_at_most_ten_percent_for_short_waits(): void
    {
        $strategy = new ExponentialRetryStrategy(3, 500, 2.0, 1000, true);
        $exception = new TooManyRequestsException($this->makeResponse(), 'slow', 429, null, 1);

        for ($iteration = 0; $iteration < 20; $iteration++) {
            $delay = $strategy->delayBeforeAttemptMs(1, $exception);
            $this->assertGreaterThanOrEqual(1000, $delay);
            $this->assertLessThanOrEqual(1100, $delay);
        }
    }

    /** @test */
    public function it_integrates_with_the_client_to_retry_429_responses(): void
    {
        $adapter = new class(2) implements HttpAdapterContract {
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
                $factories = $this->factories();

                if ($this->attempts <= $this->failuresBeforeSuccess) {
                    $psrResponse = $factories->responseFactory->createResponse(429)
                        ->withHeader('Retry-After', '0')
                        ->withHeader('Content-Type', 'application/json')
                        ->withBody($factories->streamFactory->createStream(json_encode([
                            'status' => 429,
                            'title' => 'Too Many Requests',
                            'detail' => 'slow down',
                        ])));
                    $response = new Response($psrResponse, $pendingRequest->createPsrRequest(), $pendingRequest);

                    throw TooManyRequestsException::fromResponse($response);
                }

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
        $client->setRetryStrategy(new ExponentialRetryStrategy(3, 0, 1.0, 30000, false));
        $client->setAccessToken('access_test_token');

        $response = $client->send(new DynamicGetRequest('/'));

        $this->assertSame(200, $response->status());
        $this->assertSame(3, $adapter->attempts);
    }

    private function makeResponse(): Response
    {
        $trait = new class {
            use HasDefaultFactories;

            public function build()
            {
                return $this->factories()->responseFactory->createResponse(200);
            }
        };

        /** @var PendingRequest $pendingRequest */
        $pendingRequest = (new \ReflectionClass(PendingRequest::class))->newInstanceWithoutConstructor();

        return new Response(
            $trait->build(),
            $this->createMock(\Psr\Http\Message\RequestInterface::class),
            $pendingRequest
        );
    }
}
