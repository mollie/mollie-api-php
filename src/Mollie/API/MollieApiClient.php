<?php

namespace Mollie\Api;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use Mollie\Api\Endpoints\MethodEndpoint;
use Mollie\Api\Endpoints\PaymentEndpoint;
use Mollie\Api\Endpoints\PaymentRefundEndpoint;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Exceptions\IncompatiblePlatform;
use Psr\Http\Message\ResponseInterface;

/**
 * Copyright (c) 2013, Mollie B.V.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR AND CONTRIBUTORS ``AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE AUTHOR OR CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
 * OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH
 * DAMAGE.
 *
 * @license     Berkeley Software Distribution License (BSD-License 2) http://www.opensource.org/licenses/bsd-license.php
 * @author      Mollie B.V. <info@mollie.com>
 * @copyright   Mollie B.V.
 * @link        https://www.mollie.com
 */
class MollieApiClient
{
    /**
     * Version of our client.
     */
    const CLIENT_VERSION = "1.9.6";

    /**
     * Endpoint of the remote API.
     */
    const API_ENDPOINT = "https://api.mollie.com";

    /**
     * Version of the remote API.
     */
    const API_VERSION = "v2";

    /**
     * HTTP Methods
     */
    const HTTP_GET = "GET";
    const HTTP_POST = "POST";
    const HTTP_DELETE = "DELETE";

    /**
     * @var ClientInterface
     */
    protected $http_client;

    /**
     * @var string
     */
    protected $api_endpoint = self::API_ENDPOINT;

    /**
     * RESTful Payments resource.
     *
     * @var PaymentEndpoint
     */
    public $payments;

    /**
     * RESTful Payments Refunds resource.
     *
     * @var PaymentRefundEndpoint
     */
    public $payments_refunds;

    /**
     * RESTful Methods resource.
     *
     * @var MethodEndpoint
     */
    public $methods;

    /**
     * @var string
     */
    protected $api_key;

    /**
     * True if an OAuth access token is set as API key.
     *
     * @var bool
     */
    protected $oauth_access;

    /**
     * @var array
     */
    protected $version_strings = [];

    /**
     * @var resource
     */
    protected $ch;

    /**
     * @var int
     */
    protected $last_http_response_status_code;

    /**
     * @param ClientInterface $http_client
     *
     * @throws IncompatiblePlatform
     */
    public function __construct(ClientInterface $http_client = null)
    {
        $this->http_client = $http_client ? $http_client : new Client();

        $compatibility_checker = new CompatibilityChecker();
        $compatibility_checker->checkCompatibility();

        $this->initializeEndpoints();

        $this->addVersionString("Mollie/" . self::CLIENT_VERSION);
        $this->addVersionString("PHP/" . phpversion());
        $this->addVersionString("Guzzle/" . ClientInterface::VERSION);
    }

    public function initializeEndpoints()
    {
        $this->payments = new PaymentEndpoint($this);
        $this->payments_refunds = new PaymentRefundEndpoint($this);
        $this->methods = new MethodEndpoint($this);
    }

    /**
     * @param string $url
     */
    public function setApiEndpoint($url)
    {
        $this->api_endpoint = rtrim(trim($url), '/');
    }

    /**
     * @return string
     */
    public function getApiEndpoint()
    {
        return $this->api_endpoint;
    }

    /**
     * @param string $api_key The Mollie API key, starting with 'test_' or 'live_'
     * @throws ApiException
     */
    public function setApiKey($api_key)
    {
        $api_key = trim($api_key);

        if (!preg_match('/^(live|test)_\w{30,}$/', $api_key)) {
            throw new ApiException("Invalid API key: '{$api_key}'. An API key must start with 'test_' or 'live_'.");
        }

        $this->api_key = $api_key;
        $this->oauth_access = false;
    }

    /**
     * @param string $access_token OAuth access token, starting with 'access_'
     * @throws ApiException
     */
    public function setAccessToken($access_token)
    {
        $access_token = trim($access_token);

        if (!preg_match('/^access_\w+$/', $access_token)) {
            throw new ApiException("Invalid OAuth access token: '{$access_token}'. An access token must start with 'access_'.");
        }

        $this->api_key = $access_token;
        $this->oauth_access = true;
    }

    /**
     * @return bool
     */
    public function usesOAuth()
    {
        return $this->oauth_access;
    }

    /**
     * @param string $version_string
     */
    public function addVersionString($version_string)
    {
        $this->version_strings[] = str_replace(array(" ", "\t", "\n", "\r"), '-', $version_string);
    }

    /**
     * Perform an http call. This method is used by the resource specific classes. Please use the $payments property to
     * perform operations on payments.
     *
     * @see $payments
     * @see $isuers
     *
     * @param string $http_method
     * @param string $api_method
     * @param string $http_body
     *
     * @return object
     * @throws ApiException
     *
     * @codeCoverageIgnore
     */
    public function performHttpCall($http_method, $api_method, $http_body = null)
    {
        if (empty($this->api_key)) {
            throw new ApiException("You have not set an API key or OAuth access token. Please use setApiKey() to set the API key.");
        }

        $url = $this->api_endpoint . "/" . self::API_VERSION . "/" . $api_method;
        $user_agent = implode(' ', $this->version_strings);

        if ($this->usesOAuth()) {
            $user_agent .= " OAuth/2.0";
        }

        $headers = [
            'Accept' => "application/json",
            'Authorization' => "Bearer {$this->api_key}",
            'User-Agent' => $user_agent,
            'X-Mollie-Client-Info' => php_uname(),
        ];

        $request = new Request($http_method, $url, $headers, $http_body);

        $response = $this->http_client->send($request);
        if (!$response) {
            throw new ApiException("Did not receive API response.");
        }

        return $this->parseResponseBody($response);
    }

    /**
     * Parse the PSR-7 Response body
     *
     * @param ResponseInterface $response
     * @return object
     * @throws ApiException
     */
    private function parseResponseBody(ResponseInterface $response)
    {
        $body = $response->getBody()->getContents();
        if (empty($body)) {
            throw new ApiException("No response body found.");
        }

        $object = @json_decode($body);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException("Unable to decode Mollie response: '{$body}'.");
        }

        if (!empty($object->error)) {
            $exception = new ApiException("Error executing API call ({$object->error->type}): {$object->error->message}.");

            if (!empty($object->error->field)) {
                $exception->setField($object->error->field);
            }

            throw $exception;
        }

        return $object;
    }
}
