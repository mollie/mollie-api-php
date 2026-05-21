<?php

declare(strict_types=1);

namespace Tests\Http;

use Mollie\Api\Contracts\HttpAdapterContract;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Exceptions\RetryableNetworkRequestException;
use Mollie\Api\Exceptions\TooManyRequestsException;
use Mollie\Api\Exceptions\ValidationException;
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
    public function should_retry_returns_true_for_retryable_network_and_429(): void
    {
        $strategy = new ExponentialRetryStrategy;

        /** @var PendingRequest $pendingRequest */
        $pendingRequest = (new \ReflectionClass(PendingRequest::class))->newInstanceWithoutConstructor();
        $networkException = new RetryableNetworkRequestException($pendingRequest, 'boom');

        $response = $this->makePsrResponse();
        $tooManyRequests = new TooManyRequestsException($response, 'slow down', 429, 7);

        $this->assertTrue($strategy->shouldRetry($networkException));
        $this->assertTrue($strategy->shouldRetry($tooManyRequests));
    }

    /** @test */
    public function should_retry_returns_false_for_non_retryable_exceptions(): void
    {
        $strategy = new ExponentialRetryStrategy;
        $response = $this->makePsrResponse();

        $validation = new ValidationException($response, '', 'bad');
        $api = new ApiException($response, 'oops', 500);

        $this->assertFalse($strategy->shouldRetry($validation));
        $this->assertFalse($strategy->shouldRetry($api));
        $this->assertFalse($strategy->shouldRetry(new \RuntimeException));
    }

    /** @test */
    public function delay_uses_retry_after_header_on_429(): void
    {
        $strategy = new ExponentialRetryStrategy(maxRetries: 3, baseDelayMs: 500, jitter: false);
        $response = $this->makePsrResponse();
        $tooMany = new TooManyRequestsException($response, 'slow', 429, 12);

        $this->assertSame(12_000, $strategy->delayBeforeAttemptMs(1, $tooMany));
    }

    /** @test */
    public function delay_falls_back_to_exponential_when_no_retry_after(): void
    {
        $strategy = new ExponentialRetryStrategy(
            maxRetries: 3,
            baseDelayMs: 100,
            multiplier: 2.0,
            jitter: false,
        );

        $this->assertSame(100, $strategy->delayBeforeAttemptMs(1));
        $this->assertSame(200, $strategy->delayBeforeAttemptMs(2));
        $this->assertSame(400, $strategy->delayBeforeAttemptMs(3));
    }

    /** @test */
    public function delay_is_capped_by_max_delay(): void
    {
        $strategy = new ExponentialRetryStrategy(
            maxRetries: 10,
            baseDelayMs: 1_000,
            multiplier: 10.0,
            maxDelayMs: 5_000,
            jitter: false,
        );

        $this->assertSame(5_000, $strategy->delayBeforeAttemptMs(5));
    }

    /** @test */
    public function jitter_stays_within_computed_bound(): void
    {
        $strategy = new ExponentialRetryStrategy(
            maxRetries: 3,
            baseDelayMs: 1_000,
            multiplier: 2.0,
            jitter: true,
        );

        for ($i = 0; $i < 20; $i++) {
            $delay = $strategy->delayBeforeAttemptMs(1);
            $this->assertGreaterThanOrEqual(0, $delay);
            $this->assertLessThanOrEqual(1_000, $delay);
        }
    }

    /** @test */
    public function integrates_with_client_to_retry_on_429_using_retry_after(): void
    {
        $attempts = 2; // fail twice then succeed
        $adapter = new class($attempts) implements HttpAdapterContract {
            use HasDefaultFactories;

            public int $attempts = 0;

            public function __construct(private int $failuresBeforeSuccess)
            {
            }

            public function sendRequest(PendingRequest $pendingRequest): Response
            {
                $this->attempts++;

                if ($this->attempts <= $this->failuresBeforeSuccess) {
                    $factories = $this->factories();
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

                $factories = $this->factories();
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
        $client->setRetryStrategy(new ExponentialRetryStrategy(
            maxRetries: 3,
            baseDelayMs: 0,
            multiplier: 1.0,
            jitter: false,
        ));
        $client->setAccessToken('access_test_token');

        $response = $client->send(new DynamicGetRequest('/'));

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(200, $response->status());
        $this->assertSame(3, $adapter->attempts);
    }

    /** @test */
    public function does_not_retry_non_retryable_exceptions(): void
    {
        $adapter = new class implements HttpAdapterContract {
            use HasDefaultFactories;

            public int $attempts = 0;

            public function sendRequest(PendingRequest $pendingRequest): Response
            {
                $this->attempts++;
                $factories = $this->factories();
                $psrResponse = $factories->responseFactory->createResponse(422)
                    ->withHeader('Content-Type', 'application/json')
                    ->withBody($factories->streamFactory->createStream(json_encode([
                        'status' => 422,
                        'title' => 'Unprocessable Entity',
                        'detail' => 'bad',
                    ])));

                $response = new Response($psrResponse, $pendingRequest->createPsrRequest(), $pendingRequest);

                throw new ValidationException($response, '', 'bad');
            }

            public function version(): string
            {
                return 'test/adapter';
            }
        };

        $client = new MollieApiClient($adapter);
        $client->setRetryStrategy(new ExponentialRetryStrategy(maxRetries: 3, baseDelayMs: 0, jitter: false));
        $client->setAccessToken('access_test_token');

        $this->expectException(ValidationException::class);

        try {
            $client->send(new DynamicGetRequest('/'));
        } finally {
            $this->assertSame(1, $adapter->attempts);
        }
    }

    /** @test */
    public function retries_retryable_network_errors_with_exponential_backoff(): void
    {
        $adapter = new class implements HttpAdapterContract {
            use HasDefaultFactories;

            public int $attempts = 0;

            public function sendRequest(PendingRequest $pendingRequest): Response
            {
                $this->attempts++;

                throw new RetryableNetworkRequestException($pendingRequest, 'temp');
            }

            public function version(): string
            {
                return 'test/adapter';
            }
        };

        $client = new MollieApiClient($adapter);
        $client->setRetryStrategy(new ExponentialRetryStrategy(
            maxRetries: 2,
            baseDelayMs: 0,
            jitter: false,
        ));
        $client->setAccessToken('access_test_token');

        $this->expectException(RetryableNetworkRequestException::class);

        try {
            $client->send(new DynamicGetRequest('/'));
        } finally {
            // 1 initial + 2 retries
            $this->assertSame(3, $adapter->attempts);
        }
    }

    private function makePsrResponse(): Response
    {
        $trait = new class {
            use HasDefaultFactories;

            public function build(): array
            {
                $factories = $this->factories();

                return [$factories->responseFactory->createResponse(200), $factories];
            }
        };

        [$psr] = $trait->build();
        /** @var PendingRequest $pending */
        $pending = (new \ReflectionClass(PendingRequest::class))->newInstanceWithoutConstructor();
        $request = $this->createMock(\Psr\Http\Message\RequestInterface::class);

        return new Response($psr, $request, $pending);
    }
}
