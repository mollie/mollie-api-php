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
            || $this->retryAfterDelayMs($exception) !== null;
    }

    public function delayBeforeAttemptMs(int $attempt, ?Throwable $exception = null): int
    {
        if ($exception instanceof TooManyRequestsException) {
            $delay = $this->retryAfterDelayMs($exception);

            if ($delay !== null) {
                return $this->addRetryAfterJitter($delay);
            }
        }

        $delay = $this->exponentialDelayMs($attempt);

        if ($this->jitter && $delay > 0) {
            // Full jitter — random value in [0, delay].
            return random_int(0, $delay);
        }

        return $delay;
    }

    private function retryAfterDelayMs(TooManyRequestsException $exception): ?int
    {
        if ($exception->retryAfterSeconds === null) {
            return null;
        }

        $retryAfterSeconds = max(0, $exception->retryAfterSeconds);

        if ($retryAfterSeconds > intdiv(PHP_INT_MAX, 1000)) {
            return null;
        }

        $delayMs = $retryAfterSeconds * 1000;

        return $delayMs <= $this->maxDelayMs ? $delayMs : null;
    }

    private function addRetryAfterJitter(int $delayMs): int
    {
        if (! $this->jitter || $delayMs === 0) {
            return $delayMs;
        }

        $maxJitter = min(intdiv($delayMs, 10), 1000, PHP_INT_MAX - $delayMs);

        return $delayMs + random_int(0, $maxJitter);
    }

    private function exponentialDelayMs(int $attempt): int
    {
        if ($this->baseDelayMs === 0 || $this->maxDelayMs === 0) {
            return 0;
        }

        $attempt = max(1, $attempt);
        $delay = $this->baseDelayMs * ($this->multiplier ** ($attempt - 1));

        if (! is_finite($delay) || $delay >= $this->maxDelayMs) {
            return $this->maxDelayMs;
        }

        return (int) round($delay);
    }
}
