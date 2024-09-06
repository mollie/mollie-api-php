<?php

namespace Mollie\Api\Helpers;

use Closure;

class Handler
{
    private Closure $callback;

    private string $priority;

    public function __construct(Closure $callback, string $priority)
    {
        $this->callback = $callback;
        $this->priority = $priority;
    }

    public function callback(): callable
    {
        return $this->callback;
    }

    public function priority(): string
    {
        return $this->priority;
    }
}
