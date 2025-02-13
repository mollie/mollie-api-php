<?php

namespace Mollie\Api\Http\Adapter;

use Composer\CaBundle\CaBundle;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Types\Method;

class CurlFactory
{
    public const DEFAULT_TIMEOUT = 10;

    public const DEFAULT_CONNECT_TIMEOUT = 2;

    private $handle;

    private PendingRequest $pendingRequest;

    private function __construct($handle, PendingRequest $pendingRequest)
    {
        $this->handle = $handle;
        $this->pendingRequest = $pendingRequest;
        $this->setDefaultOptions();
    }

    public static function new(string $url, PendingRequest $pendingRequest): self
    {
        $handle = curl_init($url);
        if ($handle === false) {
            throw new CurlInitializationException($pendingRequest, 'Failed to initialize CURL');
        }

        return new self($handle, $pendingRequest);
    }

    private function setDefaultOptions(): self
    {
        $this->setOption(CURLOPT_RETURNTRANSFER, true);
        $this->setOption(CURLOPT_HEADER, true);
        $this->setOption(CURLOPT_CONNECTTIMEOUT, self::DEFAULT_CONNECT_TIMEOUT);
        $this->setOption(CURLOPT_TIMEOUT, self::DEFAULT_TIMEOUT);
        $this->setOption(CURLOPT_SSL_VERIFYPEER, true);
        $this->setOption(CURLOPT_CAINFO, CaBundle::getBundledCaBundlePath());

        return $this;
    }

    public function withHeaders(array $headers): self
    {
        $headers['Content-Type'] = 'application/json';
        $this->setOption(CURLOPT_HTTPHEADER, $this->parseHeaders($headers));

        return $this;
    }

    public function withMethod(string $method, ?string $body): self
    {
        switch ($method) {
            case Method::POST:
                $this->setOption(CURLOPT_POST, true);
                $this->setOption(CURLOPT_POSTFIELDS, $body);

                break;

            case Method::PATCH:
                $this->setOption(CURLOPT_CUSTOMREQUEST, Method::PATCH);
                $this->setOption(CURLOPT_POSTFIELDS, $body);

                break;

            case Method::DELETE:
                $this->setOption(CURLOPT_CUSTOMREQUEST, Method::DELETE);
                $this->setOption(CURLOPT_POSTFIELDS, $body);

                break;

            case Method::GET:
            default:
                if ($method !== Method::GET) {
                    throw new \InvalidArgumentException('Invalid HTTP method: '.$method);
                }

                break;
        }

        return $this;
    }

    public function create()
    {
        return $this->handle;
    }

    /**
     * @param  mixed  $value
     */
    private function setOption(int $option, $value): void
    {
        if (curl_setopt($this->handle, $option, $value) === false) {
            throw new CurlInitializationException(
                $this->pendingRequest,
                sprintf('Failed to set CURL option %d', $option)
            );
        }
    }

    private function parseHeaders(array $headers): array
    {
        $result = [];
        foreach ($headers as $key => $value) {
            $result[] = $key.': '.$value;
        }

        return $result;
    }
}
