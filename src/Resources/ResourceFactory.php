<?php

namespace Mollie\Api\Resources;

class ResourceFactory
{
    /**
     * Create resource object from Api result
     *
     * @param object $apiResult
     * @param BaseResource $resource
     *
     * @return BaseResource
     */
    public static function createFromApiResult($apiResult, BaseResource $resource)
    {
        foreach ($apiResult as $property => $value) {
            $resource->{$property} = $value;
        }

        return $resource;
    }

}