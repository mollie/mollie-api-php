<?php

namespace Mollie\Api\Contracts;

interface SupportsDebuggingContract
{
    /**
     * Enable debugging for the current request.
     *
     * @return HttpAdapterContract|Connector
     */
    public function enableDebugging();

    /**
     * Disable debugging for the current request.
     *
     * @return HttpAdapterContract|Connector
     */
    public function disableDebugging();
}
