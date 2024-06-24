<?php

namespace Mollie\Api\HttpAdapter;

trait IsDebuggable
{
    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * Enable debugging. If debugging mode is enabled, the request will
     * be included in the ApiException. By default, debugging is disabled to prevent
     * sensitive request data from leaking into exception logs.
     *
     * @return $this
     */
    public function enableDebugging(): MollieHttpAdapterInterface
    {
        $this->debug = true;

        return $this;
    }

    /**
     * Disable debugging. If debugging mode is enabled, the request will
     * be included in the ApiException. By default, debugging is disabled to prevent
     * sensitive request data from leaking into exception logs.
     *
     * @return $this
     */
    public function disableDebugging(): MollieHttpAdapterInterface
    {
        $this->debug = false;

        return $this;
    }

    /**
     * Whether debugging is enabled. If debugging mode is enabled, the request will
     * be included in the ApiException. By default, debugging is disabled to prevent
     * sensitive request data from leaking into exception logs.
     *
     * @return bool
     */
    public function debuggingIsActive(): bool
    {
        return $this->debug;
    }
}
