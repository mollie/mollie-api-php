<?php

declare(strict_types=1);

namespace Mollie\Api\Http;

use Mollie\Api\Contracts\RetryStrategyContract;
use Mollie\Api\Exceptions\RetryableNetworkRequestException;
use Mollie\Api\Exceptions\TooManyRequestsException;
use Throwable;

/**
 * Exponential backoff retry strategy with optional jitter.
 *
 * Retries {@see RetryableNetworkRequestException} (temporary network issues)
 * and {@see TooManyRequestsException} (429 rate limits). Other exceptions
 * propagate immediately — validation, authentication, and other 4xx client
 * errors are permanent failures.
 *
 * Delay formula for attempt N (starting at 1):
 *   baseDelayMs * (multiplier ** (N - 1)) [+ optional jitter, capped at maxDelayMs]
 *
 * When retrying a {@see TooManyRequestsException} with a numeric Retry-After
 * header, the strategy honours the header value instead of the computed
 * exponential delay.
 */
class ExponentialRetryStrategy implements RetryStrategyContract
{
    protected int $maxRetries;

    protected int $baseDelayMs;

    protected float $multiplier;

    protected int $maxDelayMs;

    protected bool $jitter;

    public function __construct(
        int $maxRetries = 3,
        int $baseDelayMs = 500,
        float $multiplier = 2.0,
        int $maxDelayMs = 30_000,
        bool $jitter = true,
    ) {
        $this->maxRetries = max(0, $maxRetries);
        $this->baseDelayMs = max(0, $baseDelayMs);
        $this->multiplier = max(1.0, $multiplier);
        $this->maxDelayMs = max(0, $maxDelayMs);
        $this->jitter = $jitter;
    }

    public function maxRetries(): int
    {
        return $this->maxRetries;
    }

    public function shouldRetry(Throwable $exception): bool
    {
        return $exception instanceof RetryableNetworkRequestException
            || $exception instanceof TooManyRequestsException;
    }

    public function delayBeforeAttemptMs(int $attempt, ?Throwable $exception = null): int
    {
        // Honour Retry-After on 429 when the server told us how long to wait.
        if ($exception instanceof TooManyRequestsException) {
            $retryAfter = $exception->retryAfterSeconds;

            if ($retryAfter !== null) {
                return max(0, $retryAfter) * 1000;
            }
        }

        $attempt = max(1, $attempt);
        $delay = (int) round($this->baseDelayMs * ($this->multiplier ** ($attempt - 1)));

        if ($this->jitter && $delay > 0) {
            // Full jitter — random value in [0, delay].
            $delay = random_int(0, $delay);
        }

        return min($delay, $this->maxDelayMs);
    }
}
