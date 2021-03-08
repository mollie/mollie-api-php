<?php

namespace Mollie\Api\HttpAdapter;

interface MollieHttpAdapter
{
    /**
     * The version number for the underlying http client.
     * @example Guzzle/6.3
     *
     * @return string|null
     */
    public function versionString();
}
