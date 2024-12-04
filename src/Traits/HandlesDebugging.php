<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Contracts\Connector;
use Mollie\Api\Contracts\SupportsDebuggingContract;
use Mollie\Api\Exceptions\HttpAdapterDoesNotSupportDebuggingException;

/**
 * @mixin MollieApiClient
 */
trait HandlesDebugging
{
    /**
     * Enable debugging mode.
     *
     * @throws \Mollie\Api\Exceptions\HttpAdapterDoesNotSupportDebuggingException
     */
    public function enableDebugging(): Connector
    {
        $this->setDebugging(true);

        return $this;
    }

    /**
     * Disable debugging mode.
     *
     * @throws \Mollie\Api\Exceptions\HttpAdapterDoesNotSupportDebuggingException
     */
    public function disableDebugging(): Connector
    {
        $this->setDebugging(false);

        return $this;
    }

    /**
     * Toggle debugging mode. If debugging mode is enabled, the attempted request will be included in the ApiException.
     * By default, debugging is disabled to prevent leaking sensitive request data into exception logs.
     *
     * @throws \Mollie\Api\Exceptions\HttpAdapterDoesNotSupportDebuggingException
     */
    public function setDebugging(bool $enable)
    {
        if (! $this->httpClient instanceof SupportsDebuggingContract) {
            throw new HttpAdapterDoesNotSupportDebuggingException(
                'Debugging is not supported by '.get_class($this->httpClient).'.'
            );
        }

        if ($enable) {
            $this->httpClient->enableDebugging();
        } else {
            $this->httpClient->disableDebugging();
        }
    }
}
