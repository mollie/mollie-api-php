<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Exceptions\MollieException;
use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Http\Middleware\MiddlewarePriority;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\RequestSanitizer;
use Mollie\Api\Http\Response;
use Mollie\Api\Utils\Debugger;

/**
 * @mixin HasMiddleware
 */
trait HandlesDebugging
{
    private bool $hasSanitizerMiddleware = false;

    /**
     * Enable request debugging with an optional custom debugger.
     *
     * @param  callable|null  $debugger  Custom request debugger function
     * @param  bool  $die  Whether to die after dumping
     * @return $this
     */
    public function debugRequest(?callable $debugger = null, bool $die = false): self
    {
        $this->removeSensitiveData();

        $debugger ??= fn (...$args) => Debugger::symfonyRequestDebugger(...$args);

        $this->middleware()->onRequest(function (PendingRequest $pendingRequest) use ($debugger, $die): PendingRequest {
            $debugger($pendingRequest, $pendingRequest->createPsrRequest());

            if ($die) {
                Debugger::die();
            }

            return $pendingRequest;
        }, MiddlewarePriority::LOW);

        return $this;
    }

    /**
     * Enable response debugging with an optional custom debugger.
     *
     * @param  callable|null  $debugger  Custom response debugger function
     * @param  bool  $die  Whether to die after dumping
     * @return $this
     */
    public function debugResponse(?callable $debugger = null, bool $die = false): self
    {
        $this->removeSensitiveData();

        $debugger ??= fn (...$args) => Debugger::symfonyResponseDebugger(...$args);

        $this->middleware()->onResponse(function (Response $response) use ($debugger, $die): Response {
            $debugger($response, $response->getPsrResponse());

            if ($die) {
                Debugger::die();
            }

            return $response;
        }, MiddlewarePriority::HIGH);

        return $this;
    }

    /**
     * Remove sensitive data from the request and response.
     *
     * @return $this
     */
    protected function removeSensitiveData(): self
    {
        if ($this->hasSanitizerMiddleware) {
            return $this;
        }

        $this->hasSanitizerMiddleware = true;
        $sanitizer = new RequestSanitizer;

        $this->middleware()->onFatal(function (MollieException $exception) use ($sanitizer) {
            if ($exception instanceof RequestException) {
                return $sanitizer->sanitize($exception);
            }

            return $exception;
        }, MiddlewarePriority::LOW);

        return $this;
    }

    /**
     * Enable both request and response debugging.
     *
     * @param  bool  $die  Whether to die after dumping
     * @return $this
     */
    public function debug(bool $die = false): self
    {
        return $this
            ->debugRequest()
            ->debugResponse(null, $die);
    }
}
