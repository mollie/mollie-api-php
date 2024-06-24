<?php

namespace Mollie\Api\HttpAdapter;

interface MollieHttpAdapterPickerInterface
{
    /**
     * @param \GuzzleHttp\ClientInterface|MollieHttpAdapterInterface $httpClient
     *
     * @return MollieHttpAdapterInterface
     */
    public function pickHttpAdapter($httpClient): MollieHttpAdapterInterface;
}
