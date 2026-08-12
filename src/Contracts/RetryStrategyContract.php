<?php

namespace Mollie\Api\Contracts;

use Throwable;

/**
 * Defines how retries are performed for retryable network errors.
 */
interface RetryStrategyContract
{
    /**
     * The maximum number of retries after the initial attempt.
     */
    public function maxRetries(): int;

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
