<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Http\Middleware;

trait HasMiddleware
{
    protected Middleware $middleware;

    public function middleware(): Middleware
    {
        return $this->middleware ??= new Middleware;
    }
}
