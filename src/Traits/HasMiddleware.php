<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Helpers\MiddlewareHandlers;

trait HasMiddleware
{
    protected MiddlewareHandlers $middleware;

    public function middleware(): MiddlewareHandlers
    {
        return $this->middleware ??= new MiddlewareHandlers;
    }
}
