<?php

namespace Mollie\Api\Helpers;

use Mollie\Api\Contracts\ViableResponse;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;

class Handlers
{
    /**
     * @var array<Handler>
     */
    protected array $handlers = [];

    public function add(callable $handler, string $priority): void
    {
        $this->handlers[] = new Handler($handler, $priority);
    }

    public function setHandlers(array $handlers): void
    {
        $this->handlers = $handlers;
    }

    public function getHandlers(): array
    {
        return $this->handlers;
    }

    /**
     * Execute the handlers
     *
     * @param  PendingRequest|Response  $payload
     * @return PendingRequest|Response|ViableResponse
     */
    public function execute($payload)
    {
        /** @var Handler $handler */
        foreach ($this->sortHandlers() as $handler) {
            $payload = call_user_func($handler->callback(), $payload);

            /**
             * If the handler returns a value that is not an instance of PendingRequest or Response,
             * we assume that the handler has transformed the payload in some way and we return the transformed value.
             */
            if ($payload instanceof ViableResponse) {
                return $payload;
            }
        }

        return $payload;
    }

    protected function sortHandlers(): array
    {
        $highPriority = [];
        $mediumPriority = [];
        $lowPriority = [];

        $priorityMap = [
            MiddlewarePriority::HIGH => &$highPriority,
            MiddlewarePriority::MEDIUM => &$mediumPriority,
            MiddlewarePriority::LOW => &$lowPriority,
        ];

        foreach ($this->handlers as $handler) {
            $priorityMap[$handler->priority()][] = $handler;
        }

        return array_merge($highPriority, $mediumPriority, $lowPriority);
    }
}
