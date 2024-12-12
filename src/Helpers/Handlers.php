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

    public function add(callable $handler, ?string $name = null, string $priority = MiddlewarePriority::MEDIUM): void
    {
        if (in_array($name, [MiddlewarePriority::HIGH, MiddlewarePriority::MEDIUM, MiddlewarePriority::LOW])) {
            $priority = $name;
            $name = null;
        }

        if (is_string($name) && $this->handlerExists($name)) {
            throw new \InvalidArgumentException("Handler with name '{$name}' already exists.");
        }

        $this->handlers[] = new Handler($handler, $name, $priority);
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
     * @param  PendingRequest|Response|mixed  $payload
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

    private function handlerExists(string $name): bool
    {
        foreach ($this->handlers as $handler) {
            if ($handler->name() === $name) {
                return true;
            }
        }

        return false;
    }
}