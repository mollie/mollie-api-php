<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Exceptions\LogicException;
use Mollie\Api\Exceptions\MollieException;
use Mollie\Api\Exceptions\RetryableNetworkRequestException;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Request;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Utils\DataTransformer;

/**
 * @mixin MollieApiClient
 */
trait SendsRequests
{
    /**
     * The maximum number of retries for retryable network errors.
     */
    protected int $maxRetries = 5;

    /**
     * The base delay in milliseconds added per retry attempt (linear backoff).
     * Example: attempt 1 => 1000ms, attempt 2 => 2000ms, etc.
     */
    protected int $retryDelayIncreaseMs = 1000;

    /**
     * Configure the maximum number of retries for retryable network errors.
     */
    public function setMaxRetries(int $maxRetries): self
    {
        $this->maxRetries = max(0, $maxRetries);

        return $this;
    }

    /**
     * Configure the linear backoff delay increase in milliseconds.
     */
    public function setRetryDelayIncreaseMs(int $delayMs): self
    {
        $this->retryDelayIncreaseMs = max(0, $delayMs);

        return $this;
    }

    /**
     * @return mixed
     */
    public function send(Request $request)
    {
        $pendingRequest = new PendingRequest($this, $request);
        $pendingRequest = $pendingRequest->executeRequestHandlers();

        $pendingRequest = (new DataTransformer)->transform($pendingRequest);

        $lastException = null;

        for ($attempt = 0; $attempt <= $this->maxRetries; $attempt++) {
            if ($attempt > 0) {
                $delayUs = $attempt * $this->retryDelayIncreaseMs * 1000;
                usleep($delayUs);
            }

            try {
                $response = $this->httpClient->sendRequest($pendingRequest);

                return $pendingRequest->executeResponseHandlers($response);
            } catch (RetryableNetworkRequestException $e) {
                $lastException = $e;
            } catch (MollieException $exception) {
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
