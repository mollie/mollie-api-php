<?php

namespace Mollie\Api\Http;

use Mollie\Api\Contracts\MollieHttpAdapterContract;

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
    public function enableDebugging(): MollieHttpAdapterContract
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
    public function disableDebugging(): MollieHttpAdapterContract
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
