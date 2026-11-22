<?php

declare(strict_types=1);

namespace Tests\Utils;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Utils\Debugger;
use Nyholm\Psr7\Request as PsrRequest;
use Nyholm\Psr7\Response as PsrResponse;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use RuntimeException;
use Tests\Fixtures\Requests\DynamicGetRequest;

class DebuggerTest extends TestCase
{
    #[Test]
    public function default_request_debugger_reports_missing_symfony_var_dumper()
    {
        $this->withMissingSymfonyVarDumper(function (): void {
            $pendingRequest = new PendingRequest(new MockMollieClient, new DynamicGetRequest(''));
            $psrRequest = new PsrRequest('GET', 'https://api.mollie.com/v2/test');

            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage('requires symfony/var-dumper');

            Debugger::symfonyRequestDebugger($pendingRequest, $psrRequest);
        });
    }

    #[Test]
    public function default_debugger_reports_missing_symfony_var_dumper()
    {
        $this->withMissingSymfonyVarDumper(function (): void {
            $psrRequest = new PsrRequest('GET', 'https://api.mollie.com/v2/test');
            $psrResponse = new PsrResponse(200, ['X-Test' => 'yes'], '{"resource":"test"}');
            $pendingRequest = new PendingRequest(new MockMollieClient, new DynamicGetRequest(''));
            $response = new Response($psrResponse, $psrRequest, $pendingRequest);

            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage('requires symfony/var-dumper');

            Debugger::symfonyResponseDebugger($response, $psrResponse);
        });
    }

    #[Test]
    public function symfony_var_dumper_is_not_a_runtime_dependency()
    {
        $composer = json_decode((string) file_get_contents(__DIR__.'/../../composer.json'), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayNotHasKey('symfony/var-dumper', $composer['require']);
        $this->assertArrayHasKey('symfony/var-dumper', $composer['require-dev']);
        $this->assertArrayHasKey('symfony/var-dumper', $composer['suggest']);
    }

    private function withMissingSymfonyVarDumper(callable $callback): void
    {
        $availability = new ReflectionProperty(Debugger::class, 'symfonyVarDumperExists');
        $availability->setValue(null, false);

        try {
            $callback();
        } finally {
            $availability->setValue(null, null);
        }
    }
}
