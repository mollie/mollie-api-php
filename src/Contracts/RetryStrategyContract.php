<?php

declare(strict_types=1);

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
     *
     * Implementations decide which exception classes are retryable.
     * Non-retryable exceptions propagate immediately.
     */
    public function shouldRetry(Throwable $exception): bool;

    /**
     * Delay in milliseconds before performing the given retry attempt.
     *
     * The $attempt parameter starts at 1 for the first retry. Implementations
     * may inspect the triggering exception (for example, a 429's Retry-After
     * header) to decide the delay.
     */
    public function delayBeforeAttemptMs(int $attempt, ?Throwable $exception = null): int;
}
