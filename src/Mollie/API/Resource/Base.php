<?php
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
abstract class Mollie_API_Resource_Base
{
	const REST_CREATE = Mollie_API_Client::HTTP_POST;
	const REST_UPDATE = Mollie_API_Client::HTTP_POST;
	const REST_READ   = Mollie_API_Client::HTTP_GET;
	const REST_LIST   = Mollie_API_Client::HTTP_GET;
	const REST_DELETE = Mollie_API_Client::HTTP_DELETE;

	/**
	 * Default number of objects to retrieve when listing all objects.
	 */
	const DEFAULT_LIMIT = 50;

	/**
	 * @var Mollie_API_Client
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
	 * @param Mollie_API_Client $api
	 */
	public function __construct(Mollie_API_Client $api)
	{
		$this->api = $api;

		if (empty($this->resource_path))
		{
			$class_parts         = explode("_", get_class($this));
			$this->resource_path = strtolower(end($class_parts));
		}
	}

	/**
	 * @param array $filters
	 * @return string
	 * @throws Mollie_API_Exception
	 */
	private function buildQueryString (array $filters)
	{
		if (empty($filters))
		{
			return "";
		}

		// Force & because of some PHP 5.3 defaults.
		return "?" . http_build_query($filters, "", "&");
	}

	/**
	 * @param string $rest_resource
	 * @param        $body
	 * @param array $filters
	 * @return object
	 * @throws Mollie_API_Exception
	 */
	private function rest_create($rest_resource, $body, array $filters)
	{
		$result = $this->performApiCall(
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
	 * @param string $id            Id of the object to retrieve.
	 * @param array  $filters
	 * @return object
	 * @throws Mollie_API_Exception
	 */
	private function rest_read ($rest_resource, $id, array $filters)
	{
		if (empty($id))
		{
			throw new Mollie_API_Exception("Invalid resource id.");
		}

		$id     = urlencode($id);
		$result = $this->performApiCall(
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
	 * @throws Mollie_API_Exception
	 */
	private function rest_delete ($rest_resource, $id)
	{
		if (empty($id))
		{
			throw new Mollie_API_Exception("Invalid resource id.");
		}

		$id     = urlencode($id);
		$result = $this->performApiCall(
			self::REST_DELETE,
			"{$rest_resource}/{$id}"
		);

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
	 * @throws Mollie_API_Exception
	 */
	protected function rest_update ($rest_resource, $id, $body)
	{
		if (empty($id))
		{
			throw new Mollie_API_Exception("Invalid resource id.");
		}

		$id     = urlencode($id);
		$result = $this->performApiCall(
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
	 * @return Mollie_API_Object_List
	 */
	private function rest_list($rest_resource, $offset = 0, $limit = self::DEFAULT_LIMIT, array $filters)
	{
		$filters = array_merge(array("offset" => $offset, "count" => $limit), $filters);

		$api_path = $rest_resource . $this->buildQueryString($filters);

		$result = $this->performApiCall(self::REST_LIST, $api_path);

		/** @var Mollie_API_Object_List $collection */
		$collection = $this->copy($result, new Mollie_API_Object_List);

		foreach ($result->data as $data_result)
		{
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
		foreach ($api_result as $property => $value)
		{
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
	 * Create a resource with the remote API.
	 *
	 * @param array $data An array containing details on the resource. Fields supported depend on the resource created.
	 * @param array $filters
	 *
	 * @return object
	 * @throws Mollie_API_Exception
	 */
	public function create(array $data = array(), array $filters = array())
	{
		$encoded = json_encode($data);

		if (version_compare(phpversion(), "5.3.0", ">="))
		{
			if (json_last_error() != JSON_ERROR_NONE)
			{
				throw new Mollie_API_Exception("Error encoding parameters into JSON: '" . json_last_error() . "'.");
			}
		}
		else
		{
			if ($encoded === FALSE)
			{
				throw new Mollie_API_Exception("Error encoding parameters into JSON.");
			}
		}

		return $this->rest_create($this->getResourcePath(), $encoded, $filters);
	}

	/**
	 * Retrieve information on a single resource from Mollie.
	 *
	 * Will throw a Mollie_API_Exception if the resource cannot be found.
	 *
	 * @param string $resource_id
	 * @param array  $filters
	 *
	 * @return object
	 * @throws Mollie_API_Exception
	 */
	public function get ($resource_id, array $filters = array())
	{
		return $this->rest_read($this->getResourcePath(), $resource_id, $filters);
	}

	/**
	 * Delete a single resource from Mollie.
	 *
	 * Will throw a Mollie_API_Exception if the resource cannot be found.
	 *
	 * @param string $resource_id
	 *
	 * @return object
	 * @throws Mollie_API_Exception
	 */
	public function delete ($resource_id)
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
	 * @return Mollie_API_Object_List
	 */
	public function all ($offset = 0, $limit = 0, array $filters = array())
	{
		return $this->rest_list($this->getResourcePath(), $offset, $limit, $filters);
	}

	/**
	 * Perform an API call, and interpret the results and convert them to correct objects.
	 *
	 * @param      $http_method
	 * @param      $api_method
	 * @param null $http_body
	 *
	 * @return object
	 * @throws Mollie_API_Exception
	 */
	protected function performApiCall($http_method, $api_method, $http_body = NULL)
	{
		$body = $this->api->performHttpCall($http_method, $api_method, $http_body);

		if (empty($body))
		{
			throw new Mollie_API_Exception("Unable to decode Mollie response: '{$body}'.");
		}

		$object = @json_decode($body);

		if (json_last_error() != JSON_ERROR_NONE)
		{
			throw new Mollie_API_Exception("Unable to decode Mollie response: '{$body}'.");
		}

		if (!empty($object->error))
		{
			$exception = new Mollie_API_Exception("Error executing API call ({$object->error->type}): {$object->error->message}.");

			if (!empty($object->error->field))
			{
				$exception->setField($object->error->field);
			}

			throw $exception;
		}

		return $object;
	}

	/**
	 * @param string $resource_path
	 */
	public function setResourcePath ($resource_path)
	{
		$this->resource_path = strtolower($resource_path);
	}

	/**
	 * @return string
	 * @throws Mollie_API_Exception
	 */
	public function getResourcePath ()
	{
		if (strpos($this->resource_path, "_") !== FALSE)
		{
			list($parent_resource, $child_resource) = explode("_", $this->resource_path, 2);

			if (!strlen($this->parent_id))
			{
				throw new Mollie_API_Exception("Subresource '{$this->resource_path}' used without parent '$parent_resource' ID.");
			}

			return "$parent_resource/{$this->parent_id}/$child_resource";
		}

		return $this->resource_path;
	}

	/**
	 * @param string $parent_id
	 * @return $this
	 */
	public function withParentId ($parent_id)
	{
		$this->parent_id = $parent_id;

		return $this;
	}

	/**
	 * Set the resource to use a certain parent. Use this method before performing a get() or all() call.
	 *
	 * @param Mollie_API_Object_Payment|object $parent An object with an 'id' property
	 * @return $this
	 */
	public function with ($parent)
	{
		return $this->withParentId($parent->id);
	}
}
