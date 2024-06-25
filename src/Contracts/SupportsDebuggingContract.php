<?php

namespace Mollie\Api\Contracts;

interface SupportsDebuggingContract
{
    /**
     * Enable debugging for the current request.
     *
     * @return $this
     */
    public function enableDebugging();

    /**
     * Disable debugging for the current request.
     *
     * @return $this
     */
    public function disableDebugging();
}
