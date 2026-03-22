<?php

namespace Mollie\Api\Contracts;

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
     * Delay in milliseconds before performing the given retry attempt.
     *
     * The $attempt parameter starts at 1 for the first retry.
     */
    public function delayBeforeAttemptMs(int $attempt): int;
}
