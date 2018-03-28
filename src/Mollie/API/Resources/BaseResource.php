<?php

namespace Mollie\Api\Resources;


use Mollie\Api\MollieApiClient;

abstract class BaseResource
{

    protected $client;

    /**
     * @param $client
     */
    public function __construct(MollieApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * Copy the results received from the API into the PHP objects that we use.
     *
     * @param object $apiResult
     * @param object $object
     *
     * @return object
     */
    protected function copy($apiResult, $object)
    {
        foreach ($apiResult as $property => $value) {
            $object->$property = $value;
        }

        return $object;
    }
}