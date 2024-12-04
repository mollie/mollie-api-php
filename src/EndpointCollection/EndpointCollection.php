<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Contracts\Connector;
use Mollie\Api\Http\Request;

abstract class EndpointCollection
{
    protected Connector $connector;

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;

        /**
         * Default hydration decision to true to maintain legacy compatibility.
         */
        $connector::setAutoHydrate();
    }

    /**
     * @return mixed
     */
    protected function send(Request $request)
    {
        return $this->connector->send($request);
    }
}
