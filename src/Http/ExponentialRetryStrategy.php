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
 * A numeric Retry-After is honoured only when it fits within maxDelayMs. With
 * jitter enabled, up to 10% (capped at 1000ms) is added without reducing the
 * server-mandated wait.
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
        if (! $exception instanceof TooManyRequestsException) {
            return $exception instanceof RetryableNetworkRequestException;
        }

        return $exception->retryAfterSeconds === null
            || max(0, $exception->retryAfterSeconds) <= intdiv($this->maxDelayMs, 1000);
    }

    public function delayBeforeAttemptMs(int $attempt, ?Throwable $exception = null): int
    {
        // Honour Retry-After on 429 when the server told us how long to wait.
        if ($exception instanceof TooManyRequestsException) {
            $retryAfter = $exception->retryAfterSeconds;

            if ($retryAfter !== null) {
                $delay = max(0, $retryAfter) * 1000;

                if ($this->jitter && $delay > 0) {
                    $maxJitter = min(intdiv($delay, 10), 1000, PHP_INT_MAX - $delay);
                    $delay += random_int(0, $maxJitter);
                }

                return $delay;
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
