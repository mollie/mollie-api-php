<?php

namespace Mollie\Api\Contracts;

interface DataResolver
{
    /**
     * @return mixed
     */
    public function resolve();
}
