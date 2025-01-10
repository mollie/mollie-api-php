<?php

namespace Mollie\Api\Http\Adapter;

use Mollie\Api\Exceptions\MollieException;

class CurlException extends MollieException
{
    protected int $curlErrorNumber;

    public function __construct(
        string $message = '',
        int $curlErrorNumber = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $curlErrorNumber, $previous);
        $this->curlErrorNumber = $curlErrorNumber;
    }

    public function getCurlErrorNumber(): int
    {
        return $this->curlErrorNumber;
    }
}
