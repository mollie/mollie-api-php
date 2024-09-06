<?php

namespace Mollie\Api\Helpers;

use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;

class Handlers
{
    protected array $handlers = [];

    public function add(callable $handler): void
    {
        $this->handlers[] = $handler;
    }

    /**
     * Execute the handlers
     *
     * @param  PendingRequest|Response  $payload
     * @return PendingRequest|Response
     */
    public function execute($payload)
    {
        foreach ($this->handlers as $handler) {
            $payload = $handler($payload);
        }

        return $payload;
    }
}
