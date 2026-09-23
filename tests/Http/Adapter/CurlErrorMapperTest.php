<?php

declare(strict_types=1);

namespace Tests\Http\Adapter;

use Mollie\Api\Exceptions\NetworkRequestException;
use Mollie\Api\Exceptions\RetryableNetworkRequestException;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Http\Adapter\CurlErrorMapper;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CurlErrorMapperTest extends TestCase
{
    #[DataProvider('curlErrorMappings')]
    #[Test]
    public function maps_known_curl_errors_to_descriptive_network_exceptions(int $code, string $category, string $expectedException): void
    {
        $exception = CurlErrorMapper::toException($code, 'Curl error: details', $this->pendingRequest());

        $this->assertInstanceOf($expectedException, $exception);
        if ($expectedException === NetworkRequestException::class) {
            $this->assertNotInstanceOf(RetryableNetworkRequestException::class, $exception);
        }

        $this->assertSame($category.': Curl error: details', $exception->getMessage());
        $this->assertSame($category.': Curl error: details', $exception->getPlainMessage());
    }

    #[DataProvider('retryableErrorClassifications')]
    #[Test]
    public function classifies_retryable_curl_errors(int $code, bool $retryable): void
    {
        $this->assertSame($retryable, CurlErrorMapper::isRetryableError($code));
    }

    #[Test]
    public function falls_back_to_unknown_error_for_unmapped_curl_codes(): void
    {
        $exception = CurlErrorMapper::toException(999_999, 'Curl error: unexpected failure', $this->pendingRequest());

        $this->assertInstanceOf(NetworkRequestException::class, $exception);
        $this->assertNotInstanceOf(RetryableNetworkRequestException::class, $exception);
        $this->assertSame('Unknown error: Curl error: unexpected failure', $exception->getMessage());
        $this->assertFalse(CurlErrorMapper::isRetryableError(999_999));
    }

    public static function curlErrorMappings(): array
    {
        return [
            'DNS resolution failed' => [CURLE_COULDNT_RESOLVE_HOST, 'DNS resolution failed', RetryableNetworkRequestException::class],
            'Proxy resolution failed' => [CURLE_COULDNT_RESOLVE_PROXY, 'Proxy resolution failed', NetworkRequestException::class],
            'Connection failed' => [CURLE_COULDNT_CONNECT, 'Connection failed', RetryableNetworkRequestException::class],
            'Connection timed out' => [CURLE_OPERATION_TIMEOUTED, 'Connection timed out', RetryableNetworkRequestException::class],
            'Empty response' => [CURLE_GOT_NOTHING, 'Empty response', RetryableNetworkRequestException::class],
            'Network data transfer failed' => [CURLE_RECV_ERROR, 'Network data transfer failed', RetryableNetworkRequestException::class],
            'SSL connection failed' => [CURLE_SSL_CONNECT_ERROR, 'SSL connection failed', NetworkRequestException::class],
            'SSL certificate invalid' => [CURLE_SSL_CERTPROBLEM, 'SSL certificate invalid', NetworkRequestException::class],
            'SSL cipher error' => [CURLE_SSL_CIPHER, 'SSL cipher error', NetworkRequestException::class],
            'SSL CA certificate invalid' => [CURLE_SSL_CACERT, 'SSL CA certificate invalid', NetworkRequestException::class],
            'Invalid protocol' => [CURLE_UNSUPPORTED_PROTOCOL, 'Invalid protocol', NetworkRequestException::class],
            'Invalid URL' => [CURLE_URL_MALFORMAT, 'Invalid URL', NetworkRequestException::class],
            'Invalid content encoding' => [CURLE_BAD_CONTENT_ENCODING, 'Invalid content encoding', NetworkRequestException::class],
            'Out of memory' => [CURLE_OUT_OF_MEMORY, 'Out of memory', NetworkRequestException::class],
        ];
    }

    public static function retryableErrorClassifications(): array
    {
        return [
            'DNS resolution failed' => [CURLE_COULDNT_RESOLVE_HOST, true],
            'Proxy resolution failed' => [CURLE_COULDNT_RESOLVE_PROXY, false],
            'Connection failed' => [CURLE_COULDNT_CONNECT, true],
            'Connection timed out' => [CURLE_OPERATION_TIMEOUTED, true],
            'Empty response' => [CURLE_GOT_NOTHING, true],
            'Network data transfer failed' => [CURLE_RECV_ERROR, true],
            'SSL connection failed' => [CURLE_SSL_CONNECT_ERROR, false],
            'SSL certificate invalid' => [CURLE_SSL_CERTPROBLEM, false],
            'SSL cipher error' => [CURLE_SSL_CIPHER, false],
            'SSL CA certificate invalid' => [CURLE_SSL_CACERT, false],
            'Invalid protocol' => [CURLE_UNSUPPORTED_PROTOCOL, false],
            'Invalid URL' => [CURLE_URL_MALFORMAT, false],
            'Invalid content encoding' => [CURLE_BAD_CONTENT_ENCODING, false],
            'Out of memory' => [CURLE_OUT_OF_MEMORY, false],
        ];
    }

    private function pendingRequest(): PendingRequest
    {
        return new PendingRequest(new MockMollieClient, new DynamicGetRequest('/v2/payments'));
    }
}
