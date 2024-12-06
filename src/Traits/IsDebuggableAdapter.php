<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Contracts\HttpAdapterContract;

trait IsDebuggableAdapter
{
    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * Enable debugging. If debugging mode is enabled, the request will
     * be included in the ApiException. By default, debugging is disabled to prevent
     * sensitive request data from leaking into exception logs.
     */
    public function enableDebugging(): HttpAdapterContract
    {
        $this->debug = true;

        return $this;
    }

    /**
     * Disable debugging. If debugging mode is enabled, the request will
     * be included in the ApiException. By default, debugging is disabled to prevent
     * sensitive request data from leaking into exception logs.
     */
    public function disableDebugging(): HttpAdapterContract
    {
        $this->debug = false;

        return $this;
    }

    /**
     * Whether debugging is enabled. If debugging mode is enabled, the request will
     * be included in the ApiException. By default, debugging is disabled to prevent
     * sensitive request data from leaking into exception logs.
     */
    public function debuggingIsActive(): bool
    {
        return $this->debug;
    }
}
