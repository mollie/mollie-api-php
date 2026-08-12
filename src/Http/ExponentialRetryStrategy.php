<?php

namespace Mollie\Api\Http;

use Mollie\Api\Contracts\ConditionalRetryStrategyContract;
use Mollie\Api\Exceptions\RetryableNetworkRequestException;
use Mollie\Api\Exceptions\TooManyRequestsException;
use Throwable;

/**
 * Exponential backoff with optional jitter and rate-limit awareness.
 */
class ExponentialRetryStrategy implements ConditionalRetryStrategyContract
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
        int $maxDelayMs = 30000,
        bool $jitter = true
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
        if ($exception instanceof RetryableNetworkRequestException) {
            return true;
        }

        if (! $exception instanceof TooManyRequestsException) {
            return false;
        }

        return $exception->getRetryAfterSeconds() === null
            || $this->retryAfterDelayMs($exception) !== null;
    }

    public function delayBeforeAttemptMs(int $attempt, ?Throwable $exception = null): int
    {
        if ($exception instanceof TooManyRequestsException) {
            $retryAfterDelayMs = $this->retryAfterDelayMs($exception);

            if ($retryAfterDelayMs !== null) {
                return $this->addBoundedJitter($retryAfterDelayMs);
            }
        }

        $attempt = max(1, $attempt);
        $delay = (int) round($this->baseDelayMs * ($this->multiplier ** ($attempt - 1)));

        if ($this->jitter && $delay > 0) {
            $delay = random_int(0, $delay);
        }

        return min($delay, $this->maxDelayMs);
    }

    /**
     * Return the Retry-After delay when present and within the configured budget.
     */
    private function retryAfterDelayMs(TooManyRequestsException $exception): ?int
    {
        $retryAfterSeconds = $exception->getRetryAfterSeconds();

        if ($retryAfterSeconds === null) {
            return null;
        }

        $retryAfterSeconds = max(0, $retryAfterSeconds);

        if ($retryAfterSeconds > intdiv($this->maxDelayMs, 1000)) {
            return null;
        }

        return $retryAfterSeconds * 1000;
    }

    private function addBoundedJitter(int $delayMs): int
    {
        if (! $this->jitter || $delayMs === 0) {
            return $delayMs;
        }

        $maxJitter = min(intdiv($delayMs, 10), 1000, PHP_INT_MAX - $delayMs);

        return $delayMs + random_int(0, $maxJitter);
    }
}
