<?php

namespace Mollie\Api\Contracts;

use Mollie\Api\Http\Middleware;
use Mollie\Api\Http\Request;

interface Connector extends Authenticatable, IdempotencyContract, SupportsDebuggingContract, Testable
{
    /**
     * @return mixed
     */
    public function send(Request $request);

    public function resolveBaseUrl(): string;

    public function headers(): Repository;

    public function query(): Repository;

    public function middleware(): Middleware;

    public function addVersionString($versionString): self;

    public function getVersionStrings(): array;

    public function getHttpClient(): HttpAdapterContract;
}
