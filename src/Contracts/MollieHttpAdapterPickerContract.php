<?php

namespace Mollie\Api\Contracts;

interface MollieHttpAdapterPickerContract
{
    /**
     * @param \GuzzleHttp\ClientInterface|MollieHttpAdapterContract $httpClient
     *
     * @return MollieHttpAdapterContract
     */
    public function pickHttpAdapter($httpClient): MollieHttpAdapterContract;
}
