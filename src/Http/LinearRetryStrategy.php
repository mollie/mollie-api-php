<?php

namespace Mollie\Api\Http;

use Mollie\Api\Contracts\RetryStrategyContract;

/**
 * Linear backoff retry strategy.
 *
 * Delay before retry attempt N (starting at 1) equals N * $delayIncreaseMs.
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

    public function delayBeforeAttemptMs(int $attempt): int
    {
        // $attempt starts at 1 for the first retry
        return max(0, $attempt) * $this->delayIncreaseMs;
    }
}
