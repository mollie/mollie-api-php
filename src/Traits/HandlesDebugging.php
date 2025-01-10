<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Helpers\Debugger;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;

/**
 * @mixin HasMiddleware
 */
trait HandlesDebugging
{
    /**
     * Enable request debugging with an optional custom debugger.
     *
     * @param callable|null $debugger Custom request debugger function
     * @param bool $die Whether to die after dumping
     * @return $this
     */
    public function debugRequest(?callable $debugger = null, bool $die = false): self
    {
        $debugger ??= fn(...$args) => Debugger::symfonyRequestDebugger(...$args);

        $this->middleware()->onRequest(function (PendingRequest $pendingRequest) use ($debugger, $die): PendingRequest {
            $debugger($pendingRequest, $pendingRequest->createPsrRequest());

            if ($die) {
                Debugger::die();
            }

            return $pendingRequest;
        });

        return $this;
    }

    /**
     * Enable response debugging with an optional custom debugger.
     *
     * @param callable|null $debugger Custom response debugger function
     * @param bool $die Whether to die after dumping
     * @return $this
     */
    public function debugResponse(?callable $debugger = null, bool $die = false): self
    {
        $debugger ??= fn(...$args) => Debugger::symfonyResponseDebugger(...$args);

        $this->middleware()->onResponse(function (Response $response) use ($debugger, $die): Response {
            $debugger($response, $response->getPsrResponse());

            if ($die) {
                Debugger::die();
            }

            return $response;
        },);

        return $this;
    }

    /**
     * Enable both request and response debugging.
     *
     * @param bool $die Whether to die after dumping
     * @return $this
     */
    public function debug(bool $die = false): self
    {
        return $this
            ->debugRequest()
            ->debugResponse(null, $die);
    }
}
