<?php
/**
 * A base API that can be used to extend classes from that implement any of the Mollie Universal REST apis.
 */
abstract class Mollie_Api_Base
{
	const REST_CREATE = Mollie_Api::HTTP_POST;
	const REST_READ   = Mollie_Api::HTTP_GET;
	const REST_LIST   = Mollie_Api::HTTP_GET;
	const REST_DELETE = Mollie_Api::HTTP_DELETE;

	/**
	 * @var Mollie_Api
	 */
	protected $api;

	public function __construct(Mollie_Api $api)
	{
		$this->api = $api;
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
	 * @param $rest_resource
	 * @param string $id Id of the object to retrieve.
	 *
	 * @return object
	 */
	private function rest_read($rest_resource, $id)
	{
		$id = urlencode($id);
		$result = $this->performApiCall(self::REST_READ, "{$rest_resource}/{$id}");

		return $this->copy($result, $this->getResourceObject($rest_resource));
	}

	/**
	 * Default number of objects to retrieve when listing all objects.
	 */
	const DEFAULT_LIMIT = 50;

	/**
	 * Get a collection of objects from the REST API.
	 *
	 * @param $rest_resource
	 * @param int $offset
	 * @param int $limit
	 *
	 *@return Mollie_Api_Resource_List
	 */
	private function rest_list($rest_resource, $offset = 0, $limit = self::DEFAULT_LIMIT)
	{
		$api_path = $rest_resource . "?" . http_build_query(array("offset" => $offset, "count" => $limit));

		$result = $this->performApiCall(self::REST_LIST, $api_path);

		/** @var Mollie_Api_Resource_List $collection */
		$collection = $this->copy($result, new Mollie_Api_Resource_List());

		foreach ($result->data as $data_result)
		{
			$collection[] = $this->copy($data_result, $this->getResourceObject());
		}

		return $collection;
	}

	/**
	 * Copy the results received from the API into the PHP objects that we use.
	 *
	 * @param stdClass          $api_result
	 * @param object $object
	 *
	 * @return object
	 */
	private function copy(stdClass $api_result, $object)
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
	 * @throws Mollie_Api_Exception
	 * @return object
	 */
	public function create(array $data = array())
	{
		return $this->rest_create(static::RESOURCE, json_encode($data));
	}

	/**
	 * Retrieve information on a single resource from Mollie.
	 *
	 * Will throw a Mollie_Api_Exception if the resource cannot be found.
	 *
	 * @param string $resource_id
	 *
	 * @throws Mollie_Api_Exception
	 * @return object
	 */
	public function get($resource_id)
	{
		return $this->rest_read(static::RESOURCE, $resource_id);
	}

	/**
	 * Retrieve all objects of a certain resource.
	 *
	 * @param $offset
	 * @param $limit
	 *
	 * @return Mollie_Api_Resource_List
	 */
	public function all($offset, $limit)
	{
		return $this->rest_list(static::RESOURCE, $offset, $limit);
	}

	/**
	 * Perform an API call, and interpret the results and convert them to correct objects.
	 *
	 * @param      $http_method
	 * @param      $api_method
	 * @param null $http_body
	 *
	 * @return object
	 * @throws Mollie_Api_Exception
	 */
	protected function performApiCall($http_method, $api_method, $http_body = NULL)
	{
		$body = $this->api->performHttpCall($http_method, $api_method, $http_body);

		if (!($object = @json_decode($body)))
		{
			throw new Mollie_Api_Exception("Unable to decode Mollie response: \"{$body}\".");
		}

		if (!empty($object->error))
		{
			throw new Mollie_Api_Exception("Error executing API call ({$object->error->type}): {$object->error->message}.");
		}

		return $object;
	}
}