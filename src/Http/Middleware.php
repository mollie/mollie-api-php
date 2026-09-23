<?php

declare(strict_types=1);

namespace Mollie\Api\Http;

use Mollie\Api\Exceptions\MollieException;
use Mollie\Api\Http\Middleware\Handlers;
use Mollie\Api\Http\Middleware\MiddlewarePriority;

class Middleware
{
    protected Handlers $onRequest;

    protected Handlers $onResponse;

    protected Handlers $onResolved;

    protected Handlers $onFatal;

    public function __construct()
    {
        $this->onRequest = new Handlers;
        $this->onResponse = new Handlers;
        $this->onResolved = new Handlers;
        $this->onFatal = new Handlers;
    }

    public function onRequest(callable $callback, ?string $name = null, string $priority = MiddlewarePriority::MEDIUM): self
    {
        $this->onRequest->add(static function (PendingRequest $pendingRequest) use ($callback): PendingRequest {
            $result = $callback($pendingRequest);

            if ($result instanceof PendingRequest) {
                return $result;
            }

            return $pendingRequest;
        }, $name, $priority);

        return $this;
    }

    public function onResponse(callable $callback, ?string $name = null, string $priority = MiddlewarePriority::MEDIUM): self
    {
        $this->onResponse->add(static function (Response $response) use ($callback): Response {
            $result = $callback($response);

            if ($result === null) {
                return $response;
            }

            if (! $result instanceof Response) {
                throw new \UnexpectedValueException(
                    'Response middleware must return '.Response::class.' or void; '.get_debug_type($result).' returned.'
                );
            }

            return $result;
        }, $name, $priority);

        return $this;
    }

    public function onResolved(callable $callback, ?string $name = null, string $priority = MiddlewarePriority::MEDIUM): self
    {
        $this->onResolved->add(static function ($result) use ($callback) {
            return $callback($result) ?? $result;
        }, $name, $priority);

        return $this;
    }

    public function onFatal(callable $callback, ?string $name = null, string $priority = MiddlewarePriority::MEDIUM): self
    {
        $this->onFatal->add(static function (MollieException $exception) use ($callback) {
            $result = $callback($exception);

            return $result instanceof MollieException ? $result : $exception;
        }, $name, $priority);

        return $this;
    }

    public function executeOnRequest(PendingRequest $pendingRequest): PendingRequest
    {
        return $this->onRequest->execute($pendingRequest);
    }

    public function executeOnResponse(Response $response): Response
    {
        return $this->onResponse->execute($response);
    }

    /**
     * @param  mixed  $result
     * @return mixed
     */
    public function executeOnResolved($result)
    {
        return $this->onResolved->execute($result);
    }

    public function executeOnFatal(MollieException $exception): MollieException
    {
        return $this->onFatal->execute($exception);
    }

    /**
     * @param  array<Middleware>  ...$handlersCollection
     */
    public function merge(...$handlersCollection): self
    {
        /** @var Middleware $handlers */
        foreach ($handlersCollection as $handlers) {
            $onRequestHandlers = array_merge(
                $this->onRequest->getHandlers(),
                $handlers->onRequest->getHandlers()
            );

            $this->onRequest->setHandlers($onRequestHandlers);

            $onResponseHandlers = array_merge(
                $this->onResponse->getHandlers(),
                $handlers->onResponse->getHandlers()
            );

            $this->onResponse->setHandlers($onResponseHandlers);

            $onResolvedHandlers = array_merge(
                $this->onResolved->getHandlers(),
                $handlers->onResolved->getHandlers()
            );

            $this->onResolved->setHandlers($onResolvedHandlers);

            $onFatalHandlers = array_merge(
                $this->onFatal->getHandlers(),
                $handlers->onFatal->getHandlers()
            );
            $this->onFatal->setHandlers($onFatalHandlers);
        }

        return $this;
    }
}
