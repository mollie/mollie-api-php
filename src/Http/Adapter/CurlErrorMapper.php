<?php

namespace Mollie\Api\Http\Adapter;

use Mollie\Api\Exceptions\NetworkRequestException;
use Mollie\Api\Exceptions\RetryableNetworkRequestException;
use Mollie\Api\Http\PendingRequest;

class CurlErrorMapper
{
    /**
     * Maps CURL error codes to their root causes for better error messages.
     *
     * @var array<int, string>
     */
    private const ERROR_CATEGORIES = [
        // DNS and host resolution issues
        CURLE_COULDNT_RESOLVE_HOST => 'DNS resolution failed',
        CURLE_COULDNT_RESOLVE_PROXY => 'Proxy resolution failed',

        // Connection issues
        CURLE_COULDNT_CONNECT => 'Connection failed',
        CURLE_OPERATION_TIMEOUTED => 'Connection timed out',
        CURLE_GOT_NOTHING => 'Empty response',
        CURLE_RECV_ERROR => 'Network data transfer failed',

        // SSL/TLS issues
        CURLE_SSL_CONNECT_ERROR => 'SSL connection failed',
        CURLE_SSL_CERTPROBLEM => 'SSL certificate invalid',
        CURLE_SSL_CIPHER => 'SSL cipher error',
        CURLE_SSL_CACERT => 'SSL CA certificate invalid',

        // Request issues
        CURLE_UNSUPPORTED_PROTOCOL => 'Invalid protocol',
        CURLE_URL_MALFORMAT => 'Invalid URL',
        CURLE_BAD_CONTENT_ENCODING => 'Invalid content encoding',

        // System issues
        CURLE_OUT_OF_MEMORY => 'Out of memory',
    ];

    /**
     * Errors that are likely temporary and should be retried.
     *
     * @var array<int>
     */
    private const RETRYABLE_ERRORS = [
        CURLE_OPERATION_TIMEOUTED,     // Connection or transfer timeout
        CURLE_COULDNT_RESOLVE_HOST,    // Temporary DNS issues
        CURLE_COULDNT_CONNECT,         // Server temporarily unreachable
        CURLE_GOT_NOTHING,             // Server dropped connection
        CURLE_RECV_ERROR,              // Failure receiving network data
    ];

    public static function toException(int $curlErrorNumber, string $curlErrorMessage, PendingRequest $pendingRequest): NetworkRequestException
    {
        $category = self::ERROR_CATEGORIES[$curlErrorNumber] ?? 'Unknown error';
        $message = "{$category}: {$curlErrorMessage}";

        return self::isRetryableError($curlErrorNumber)
            ? new RetryableNetworkRequestException($pendingRequest, $message)
            : new NetworkRequestException($pendingRequest, null, $message);
    }

    /**
     * Determines if a CURL error is retryable.
     */
    public static function isRetryableError(int $curlErrorNumber): bool
    {
        return in_array($curlErrorNumber, self::RETRYABLE_ERRORS, true);
    }
}
