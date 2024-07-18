<?php

namespace Mollie\Api\Contracts;

interface HasBody
{
    /**
     * Get the body of the response.
     *
     * @return string
     */
    public function getBody(): string;
}
