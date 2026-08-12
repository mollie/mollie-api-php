<?php

declare(strict_types=1);

namespace Mollie\Api\Contracts;

interface MollieHttpAdapterPickerContract
{
    /**
     * @param  \GuzzleHttp\ClientInterface|HttpAdapterContract  $httpClient
     */
    public function pickHttpAdapter($httpClient): HttpAdapterContract;
}
