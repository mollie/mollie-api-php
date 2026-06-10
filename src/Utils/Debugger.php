<?php

declare(strict_types=1);

namespace Mollie\Api\Utils;

use Closure;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class Debugger
{
    /**
     * Application "Die" handler.
     *
     * @var Closure|null
     */
    public static $dieHandler = null;

    /**
     * @var bool|null
     */
    private static ?bool $symfonyVarDumperExists = null;

    /**
     * Debug a request with Symfony Var Dumper
     */
    public static function symfonyRequestDebugger(PendingRequest $pendingRequest, RequestInterface $psrRequest): void
    {
        self::ensureSymfonyVarDumperIsAvailable();

        $headers = [];

        foreach ($psrRequest->getHeaders() as $headerName => $value) {
            $headers[$headerName] = implode(';', $value);
        }

        \Symfony\Component\VarDumper\VarDumper::dump([
            'request' => get_class($pendingRequest->getRequest()),
            'method' => $psrRequest->getMethod(),
            'uri' => (string) $psrRequest->getUri(),
            'headers' => $headers,
            'body' => (string) $psrRequest->getBody(),
        ]);
    }

    /**
     * Debug a response with Symfony Var Dumper
     */
    public static function symfonyResponseDebugger(Response $response, ResponseInterface $psrResponse): void
    {
        self::ensureSymfonyVarDumperIsAvailable();

        $headers = [];

        foreach ($psrResponse->getHeaders() as $headerName => $value) {
            $headers[$headerName] = implode(';', $value);
        }

        \Symfony\Component\VarDumper\VarDumper::dump([
            'status' => $response->status(),
            'headers' => $headers,
            'body' => json_decode((string) $psrResponse->getBody(), true),
        ]);
    }

    private static function ensureSymfonyVarDumperIsAvailable(): void
    {
        if (self::$symfonyVarDumperExists ?? class_exists(\Symfony\Component\VarDumper\VarDumper::class)) {
            return;
        }

        throw new RuntimeException(
            'Debugging with the default Symfony debugger requires symfony/var-dumper. Install symfony/var-dumper with Composer or pass a custom debug callback.'
        );
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
}
