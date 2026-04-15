<?php

declare(strict_types=1);

namespace Mollie\Api\Traits;

use Mollie\Api\Contracts\RetryStrategyContract;
use Mollie\Api\Exceptions\LogicException;
use Mollie\Api\Exceptions\MollieException;
use Mollie\Api\Http\LinearRetryStrategy;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Request;
use Mollie\Api\Http\Requests\ResourceHydratableRequest;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Utils\DataTransformer;

/**
 * @mixin MollieApiClient
 */
trait SendsRequests
{
    /**
     * Strategy that defines retry behavior.
     */
    protected RetryStrategyContract $retryStrategy;

    protected function initializeSendsRequests(): void
    {
        $this->retryStrategy = $this->retryStrategy ?? new LinearRetryStrategy();
    }

    /**
     * Set a custom retry strategy implementation.
     */
    public function setRetryStrategy(RetryStrategyContract $strategy): self
    {
        $this->retryStrategy = $strategy;

        return $this;
    }

    /**
     * Send a request and return the hydrated resource (for {@see ResourceHydratableRequest})
     * or the raw response payload for other requests.
     *
     * Static analysers infer the concrete resource type via the `TResource`
     * template bound on each concrete request class (see
     * `@extends ResourceHydratableRequest<Payment>` on `GetPaymentRequest`
     * for example). Requests that are not hydratable return `null`.
     *
     * @template TResource of object
     *
     * @param  ResourceHydratableRequest<TResource>|Request  $request
     * @return ($request is ResourceHydratableRequest<TResource> ? TResource : mixed)
     */
    public function send(Request $request)
    {
        $pendingRequest = new PendingRequest($this, $request);
        $pendingRequest = $pendingRequest->executeRequestHandlers();

        $pendingRequest = (new DataTransformer)->transform($pendingRequest);

        $lastException = null;

        for ($attempt = 0; $attempt <= $this->retryStrategy->maxRetries(); $attempt++) {
            if ($attempt > 0) {
                $delayMs = $this->retryStrategy->delayBeforeAttemptMs($attempt, $lastException);

                usleep($delayMs * 1000);
            }

            try {
                $response = $this->httpClient->sendRequest($pendingRequest);

                return $pendingRequest->executeResponseHandlers($response);
            } catch (MollieException $exception) {
                if ($this->retryStrategy->shouldRetry($exception)) {
                    $lastException = $exception;

                    continue;
                }

                $exception = $pendingRequest->executeFatalHandlers($exception);

                throw $exception;
            }
        }

        if ($lastException instanceof MollieException) {
            $lastException = $pendingRequest->executeFatalHandlers($lastException);

            throw $lastException;
        }

        // This should be unreachable, but keep a safe fallback for static analysis
        throw new LogicException('Request failed after retries without a final exception.');
    }
}
