<?php

namespace Mollie\Api\Contracts;

use Throwable;

/**
 * Defines exception-aware retry behavior.
 */
interface ConditionalRetryStrategyContract extends RetryStrategyContract
{
    /**
     * Whether a given exception should trigger a retry.
     */
    public function shouldRetry(Throwable $exception): bool;

    /**
     * Delay in milliseconds before performing the given retry attempt.
     *
     * The $attempt parameter starts at 1 for the first retry. Implementations
     * may inspect the exception that triggered the retry.
     */
    public function delayBeforeAttemptMs(int $attempt, ?Throwable $exception = null): int;
}
