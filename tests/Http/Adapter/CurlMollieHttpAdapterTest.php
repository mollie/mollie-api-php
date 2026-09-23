<?php

declare(strict_types=1);

namespace Tests\Http\Adapter;

use Mollie\Api\Http\Adapter\CurlMollieHttpAdapter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class CurlMollieHttpAdapterTest extends TestCase
{
    #[Test]
    public function parses_final_header_block_from_continue_preamble_responses(): void
    {
        $headers = "HTTP/1.1 100 Continue\r\nX-Provisional: ignored\r\n\r\n"
            ."HTTP/1.1 200 OK\r\nContent-Type: application/json\r\nX-Request-Id: req_123\r\n\r\n";
        $rawResponse = $headers.'{"ok":true}';

        [$parsedHeaders, $body, $statusCode] = $this->parseResponseDetails($rawResponse, strlen($headers), 200);

        $this->assertSame([
            'Content-Type' => 'application/json',
            'X-Request-Id' => 'req_123',
        ], $parsedHeaders);
        $this->assertSame('{"ok":true}', $body);
        $this->assertSame(200, $statusCode);
    }

    #[Test]
    public function parses_headers_without_spaces_and_continuation_lines(): void
    {
        $headers = "HTTP/1.1 200 OK\r\nX-Compact:value\r\nX-Folded: first\r\n second\r\n\r\n";
        $rawResponse = $headers.'body';

        [$parsedHeaders, $body, $statusCode] = $this->parseResponseDetails($rawResponse, strlen($headers), 200);

        $this->assertSame([
            'X-Compact' => 'value',
            'X-Folded' => 'first second',
        ], $parsedHeaders);
        $this->assertSame('body', $body);
        $this->assertSame(200, $statusCode);
    }

    /**
     * @return array{0: array<string, string>, 1: string, 2: int}
     */
    private function parseResponseDetails(string $response, int $headerSize, int $statusCode): array
    {
        $method = new ReflectionMethod(CurlMollieHttpAdapter::class, 'parseResponseDetails');
        $method->setAccessible(true);

        return $method->invoke(null, $response, $headerSize, $statusCode);
    }
}
