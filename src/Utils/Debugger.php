<?php

namespace Mollie\Api\Utils;

use Closure;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\VarDumper\VarDumper;

class Debugger
{
    /**
     * Application "Die" handler.
     *
     * @var Closure|null
     */
    public static $dieHandler = null;

    /**
     * Debug a request with Symfony Var Dumper
     */
    public static function symfonyRequestDebugger(PendingRequest $pendingRequest, RequestInterface $psrRequest): void
    {
        $headers = [];

        foreach ($psrRequest->getHeaders() as $headerName => $value) {
            $headers[$headerName] = implode(';', $value);
        }

        VarDumper::dump([
            'request' => get_class($pendingRequest->getRequest()),
            'method' => $psrRequest->getMethod(),
            'uri' => (string) $psrRequest->getUri(),
            'headers' => $headers,
            'body' => (string) $psrRequest->getBody(),
        ], 'Mollie Request (' . self::getLabel($pendingRequest->getRequest()) . ') ->');
    }

    /**
     * Debug a response with Symfony Var Dumper
     */
    public static function symfonyResponseDebugger(Response $response, ResponseInterface $psrResponse): void
    {
        $headers = [];

        foreach ($psrResponse->getHeaders() as $headerName => $value) {
            $headers[$headerName] = implode(';', $value);
        }

        VarDumper::dump([
            'status' => $response->status(),
            'headers' => $headers,
            'body' => json_decode((string) $psrResponse->getBody(), true),
        ], 'Mollie Response (' . self::getLabel($response) . ') ->');
    }

    /**
     * Kill the application
     */
    public static function die(): void
    {
        $handler = self::$dieHandler ?? static function (): int {
            exit(1);
        };

        $handler();
    }

    private static function getLabel(object $object): string
    {
        $className = explode('\\', get_class($object));

        return end($className);
    }
}
