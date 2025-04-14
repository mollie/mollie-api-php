<?php

namespace Mollie\Api\Http\Middleware;

use Closure;

class Handler
{
    private Closure $callback;

    private ?string $name;

    private string $priority;

    public function __construct(Closure $callback, ?string $name, string $priority)
    {
        $this->callback = $callback;
        $this->name = $name;
        $this->priority = $priority;
    }

    public function callback(): callable
    {
        return $this->callback;
    }

    public function name(): ?string
    {
        return $this->name;
    }

    public function priority(): string
    {
        return $this->priority;
    }
}
