<?php
/**
 * A list of Mollie_Api_Resource's that were retrieved from the API.
 */
class Mollie_Api_Resource_List extends ArrayObject
{
	/**
	 * Total number of available objects.
	 *
	 * @var int
	 */
	public $totalCount;

	/**
	 * Offset from which this list of object was created.
	 *
	 * @var int
	 */
	public $offset;

	/**
	 * Total number of objects in this list.
	 *
	 * @var int
	 */
	public $count;

	/**
	 * @var Mollie_Api_Resource[]
	 */
	public $data;
}