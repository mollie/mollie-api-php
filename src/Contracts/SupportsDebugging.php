<?php

namespace Mollie\Api\Contracts;

interface SupportsDebugging
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
