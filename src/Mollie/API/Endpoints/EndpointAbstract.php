<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\BaseCollection;
use Mollie\Api\Resources\Payment;

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
abstract class EndpointAbstract
{
    const REST_CREATE = MollieApiClient::HTTP_POST;
    const REST_UPDATE = MollieApiClient::HTTP_POST;
    const REST_READ = MollieApiClient::HTTP_GET;
    const REST_LIST = MollieApiClient::HTTP_GET;
    const REST_DELETE = MollieApiClient::HTTP_DELETE;

    /**
     * Default number of objects to retrieve when listing all objects.
     */
    const DEFAULT_LIMIT = 50;

    /**
     * @var MollieApiClient
     */
    protected $api;

    /**
     * @var string
     */
    protected $resource_path;

    /**
     * @var string|null
     */
    protected $parent_id;

    /**
     * @param MollieApiClient $api
     */
    public function __construct(MollieApiClient $api)
    {
        $this->api = $api;
    }

    /**
     * @param array $filters
     * @return string
     * @throws ApiException
     */
    private function buildQueryString(array $filters)
    {
        if (empty($filters)) {
            return "";
        }

        return "?" . http_build_query($filters, "");
    }

    /**
     * @param string $rest_resource
     * @param        $body
     * @param array $filters
     * @return object
     * @throws ApiException
     */
    private function rest_create($rest_resource, $body, array $filters)
    {
        $result = $this->api->performHttpCall(
            self::REST_CREATE,
            $rest_resource . $this->buildQueryString($filters),
            $body
        );

        return $this->copy($result, $this->getResourceObject());
    }

    /**
     * Retrieves a single object from the REST API.
     *
     * @param string $rest_resource Resource name.
     * @param string $id Id of the object to retrieve.
     * @param array $filters
     * @return object
     * @throws ApiException
     */
    private function rest_read($rest_resource, $id, array $filters)
    {
        if (empty($id)) {
            throw new ApiException("Invalid resource id.");
        }

        $id = urlencode($id);
        $result = $this->api->performHttpCall(
            self::REST_READ,
            "{$rest_resource}/{$id}" . $this->buildQueryString($filters)
        );

        return $this->copy($result, $this->getResourceObject());
    }

    /**
     * Sends a DELETE request to a single Molle API object.
     *
     * @param string $rest_resource
     * @param string $id
     *
     * @return object
     * @throws ApiException
     */
    private function rest_delete($rest_resource, $id)
    {
        if (empty($id)) {
            throw new ApiException("Invalid resource id.");
        }

        $id = urlencode($id);
        $result = $this->api->performHttpCall(
            self::REST_DELETE,
            "{$rest_resource}/{$id}"
        );

        if ($result === null) {
            return null;
        }

        return $this->copy($result, $this->getResourceObject());
    }

    /**
     * Sends a POST request to a single Molle API object to update it.
     *
     * @param string $rest_resource
     * @param string $id
     * @param string $body
     *
     * @return object
     * @throws ApiException
     */
    protected function rest_update($rest_resource, $id, $body)
    {
        if (empty($id)) {
            throw new ApiException("Invalid resource id.");
        }

        $id = urlencode($id);
        $result = $this->api->performHttpCall(
            self::REST_UPDATE,
            "{$rest_resource}/{$id}",
            $body
        );

        return $this->copy($result, $this->getResourceObject());
    }

    /**
     * Get a collection of objects from the REST API.
     *
     * @param $rest_resource
     * @param int $offset
     * @param int $limit
     * @param array $filters
     *
     * @return BaseCollection
     */
    private function rest_list($rest_resource, $offset = 0, $limit = self::DEFAULT_LIMIT, array $filters)
    {
        $filters = array_merge(["from" => $offset, "limit" => $limit], $filters);

        $api_path = $rest_resource . $this->buildQueryString($filters);

        $result = $this->api->performHttpCall(self::REST_LIST, $api_path);

        /** @var BaseCollection $collection */
        $collection = $this->getResourceCollectionObject($result->count, $result->_links);

        foreach ($result->_embedded->{$collection->getCollectionResourceName()} as $data_result) {
            $collection[] = $this->copy($data_result, $this->getResourceObject());
        }

        return $collection;
    }

    /**
     * Copy the results received from the API into the PHP objects that we use.
     *
     * @param object $api_result
     * @param object $object
     *
     * @return object
     */
    protected function copy($api_result, $object)
    {
        foreach ($api_result as $property => $value) {
            $object->$property = $value;
        }

        return $object;
    }

    /**
     * Get the object that is used by this API. Every API uses one type of object.
     *
     * @return object
     */
    abstract protected function getResourceObject();

    /**
     * Get the collection object that is used by this API. Every API uses one type of collection object.
     *
     * @param int $count
     * @param object[] $_links
     *
     * @return BaseCollection
     */
    abstract protected function getResourceCollectionObject($count, $_links);

    /**
     * Create a resource with the remote API.
     *
     * @param array $data An array containing details on the resource. Fields supported depend on the resource created.
     * @param array $filters
     *
     * @return object
     * @throws ApiException
     */
    public function create(array $data = [], array $filters = [])
    {
        $encoded = json_encode($data);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException("Error encoding parameters into JSON: '" . json_last_error() . "'.");
        }

        return $this->rest_create($this->getResourcePath(), $encoded, $filters);
    }

    /**
     * Retrieve information on a single resource from Mollie.
     *
     * Will throw a ApiException if the resource cannot be found.
     *
     * @param string $resource_id
     * @param array $filters
     *
     * @return object
     * @throws ApiException
     */
    public function get($resource_id, array $filters = [])
    {
        return $this->rest_read($this->getResourcePath(), $resource_id, $filters);
    }

    /**
     * Delete a single resource from Mollie.
     *
     * Will throw a ApiException if the resource cannot be found.
     *
     * @param string $resource_id
     *
     * @return object
     * @throws ApiException
     */
    public function delete($resource_id)
    {
        return $this->rest_delete($this->getResourcePath(), $resource_id);
    }

    /**
     * Retrieve all objects of a certain resource.
     *
     * @param int $offset
     * @param int $limit
     * @param array $filters
     *
     * @return BaseCollection
     */
    public function all($offset = 0, $limit = 0, array $filters = [])
    {
        return $this->rest_list($this->getResourcePath(), $offset, $limit, $filters);
    }

    /**
     * @param string $resource_path
     */
    public function setResourcePath($resource_path)
    {
        $this->resource_path = strtolower($resource_path);
    }

    /**
     * @return string
     * @throws ApiException
     */
    public function getResourcePath()
    {
        if (strpos($this->resource_path, "_") !== false) {
            list($parent_resource, $child_resource) = explode("_", $this->resource_path, 2);

            if (empty($this->parent_id)) {
                throw new ApiException("Subresource '{$this->resource_path}' used without parent '$parent_resource' ID.");
            }

            return "$parent_resource/{$this->parent_id}/$child_resource";
        }

        return $this->resource_path;
    }

    /**
     * @param string $parent_id
     * @return $this
     */
    public function withParentId($parent_id)
    {
        $this->parent_id = $parent_id;

        return $this;
    }

    /**
     * Set the resource to use a certain parent. Use this method before performing a get() or all() call.
     *
     * @param Payment|object $parent An object with an 'id' property
     * @return $this
     */
    public function with($parent)
    {
        return $this->withParentId($parent->id);
    }
}
