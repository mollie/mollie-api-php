<?php

namespace Mollie\Api\Contracts;

use Mollie\Api\Helpers\MiddlewareHandlers;
use Mollie\Api\Http\Request;

interface Connector extends Authenticatable, Hydratable, IdempotencyContract, SupportsDebuggingContract
{
    public function send(Request $request): ?object;

    public function resolveBaseUrl(): string;

    public function headers(): ArrayRepository;

    public function query(): ArrayRepository;

    public function middleware(): MiddlewareHandlers;

    public function addVersionString($versionString): self;

    public function getVersionStrings(): array;

    public function getHttpClient(): HttpAdapterContract;
}
