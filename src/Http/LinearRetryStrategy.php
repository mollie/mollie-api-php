<?php

declare(strict_types=1);

namespace Mollie\Api\Http;

use Mollie\Api\Contracts\RetryStrategyContract;
use Mollie\Api\Exceptions\RetryableNetworkRequestException;
use Throwable;

/**
 * Linear backoff retry strategy.
 *
 * Delay before retry attempt N (starting at 1) equals N * $delayIncreaseMs.
 *
 * Retries only {@see RetryableNetworkRequestException} — matches the v3
 * default behaviour. For 429 Too Many Requests handling, use
 * {@see ExponentialRetryStrategy}.
 */
class LinearRetryStrategy implements RetryStrategyContract
{
    protected int $maxRetries;

    protected int $delayIncreaseMs;

    public function __construct(int $maxRetries = 5, int $delayIncreaseMs = 1000)
    {
        $this->maxRetries = max(0, $maxRetries);
        $this->delayIncreaseMs = max(0, $delayIncreaseMs);
    }

    public function maxRetries(): int
    {
        return $this->maxRetries;
    }

    public function shouldRetry(Throwable $exception): bool
    {
        return $exception instanceof RetryableNetworkRequestException;
    }

    public function delayBeforeAttemptMs(int $attempt, ?Throwable $exception = null): int
    {
        // $attempt starts at 1 for the first retry
        return max(0, $attempt) * $this->delayIncreaseMs;
    }
}
