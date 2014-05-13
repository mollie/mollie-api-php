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
 * @author      Mollie B.V. <info@mollie.nl>
 * @copyright   Mollie B.V.
 * @link        https://www.mollie.nl
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
	const DEFAULT_LIMIT = 10;

	/**
	 * @var Mollie_API_Client
	 */
	protected $api;

	/**
	 * @param Mollie_API_Client $api
	 */
	public function __construct(Mollie_API_Client $api)
	{
		$this->api = $api;
	}

	/**
	 * @return string
	 */
	protected function getResourceName ()
	{
		$class_parts = explode("_", get_class($this));

		return mb_strtolower(end($class_parts));
	}

	/**
	 * @param $rest_resource
	 * @param $body
	 *
	 * @return object
	 */
	private function rest_create($rest_resource, $body)
	{
		$result = $this->performApiCall(self::REST_CREATE, $rest_resource, $body);
		return $this->copy($result, $this->getResourceObject($rest_resource));
	}

	/**
	 * Retrieves a single object from the REST API.
	 *
	 * @param string $rest_resource Resource name.
	 * @param string $id            Id of the object to retrieve.
	 * @throws Mollie_API_Exception
	 * @return object
	 */
	private function rest_read ($rest_resource, $id)
	{
		if (empty($id))
		{
			throw new Mollie_API_Exception("Invalid resource id.");
		}

		$id     = urlencode($id);
		$result = $this->performApiCall(self::REST_READ, "{$rest_resource}/{$id}");

		return $this->copy($result, $this->getResourceObject($rest_resource));
	}

	/**
	 * Get a collection of objects from the REST API.
	 *
	 * @param $rest_resource
	 * @param int $offset
	 * @param int $limit
	 *
	 * @return Mollie_API_Object_List
	 */
	private function rest_list($rest_resource, $offset = 0, $limit = self::DEFAULT_LIMIT)
	{
		$api_path = $rest_resource . "?" . http_build_query(array("offset" => $offset, "count" => $limit));

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
			if (property_exists(get_class($object), $property))
			{
				$object->$property = $value;
			}
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
	 *
	 * @throws Mollie_API_Exception
	 * @return object
	 */
	public function create(array $data = array())
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

		return $this->rest_create($this->getResourceName(), $encoded);
	}

	/**
	 * Retrieve information on a single resource from Mollie.
	 *
	 * Will throw a Mollie_API_Exception if the resource cannot be found.
	 *
	 * @param string $resource_id
	 *
	 * @throws Mollie_API_Exception
	 * @return object
	 */
	public function get($resource_id)
	{
		return $this->rest_read($this->getResourceName(), $resource_id);
	}

	/**
	 * Retrieve all objects of a certain resource.
	 *
	 * @param int $offset
	 * @param int $limit
	 *
	 * @return Mollie_API_Object_List
	 */
	public function all ($offset = 0, $limit = 0)
	{
		return $this->rest_list($this->getResourceName(), $offset, $limit);
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

		if (!($object = @json_decode($body)))
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
}
